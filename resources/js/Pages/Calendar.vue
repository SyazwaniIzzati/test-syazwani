<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Header from '@/Components/Header.vue'
import { Head } from '@inertiajs/vue3'
import { ref, computed, onMounted } from 'vue'
import FullCalendar from '@fullcalendar/vue3'
import dayGridPlugin from '@fullcalendar/daygrid'
import timeGridPlugin from '@fullcalendar/timegrid'
import listPlugin from '@fullcalendar/list'
import interactionPlugin from '@fullcalendar/interaction'

const props = defineProps({
    tasks: Array
})

// --- Priority colors, tuned to Outlook's tinted-category-pill look ---
const priorityStyles = {
    high:   { bg: '#E8F1FB', border: '#0078D4', text: '#004578' }, // blue
    medium: { bg: '#FFF4CE', border: '#C19C00', text: '#8A6D00' }, // amber
    low:    { bg: '#FCE9EC', border: '#D13438', text: '#A4262C' }, // rose
}

const events = props.tasks.map(task => {
    const style = priorityStyles[task.priority] ?? priorityStyles.low

    return {
        id: task.id,
        title: task.title,
        start: task.scheduled_time,
        end: task.time_to_complete,
        backgroundColor: style.bg,
        borderColor: style.border,
        textColor: style.text,
        extendedProps: {
            description: task.description,
            completed: task.is_completed,
            priority: task.priority
        }
    }
})

// --- Custom toolbar state (we render our own header, Outlook-style) ---
const calendarRef = ref(null)
const currentTitle = ref('')
const currentView = ref('dayGridMonth')

const viewOptions = [
    { key: 'dayGridMonth', label: 'Month' },
    { key: 'timeGridWeek', label: 'Week' },
    { key: 'timeGridDay', label: 'Day' },
    { key: 'listWeek', label: 'List' },
]

const calendarApi = () => calendarRef.value?.getApi()

const updateTitle = () => {
    const api = calendarApi()
    if (api) currentTitle.value = api.view.title
}

const goToday = () => { calendarApi()?.today(); updateTitle() }
const goPrev = () => { calendarApi()?.prev(); updateTitle() }
const goNext = () => { calendarApi()?.next(); updateTitle() }

const switchView = (viewKey) => {
    calendarApi()?.changeView(viewKey)
    currentView.value = viewKey
    updateTitle()
}

onMounted(() => {
    updateTitle()
})

// --- Tooltip state (custom hover card, Outlook-style) ---
const tooltip = ref({
    visible: false,
    x: 0,
    y: 0,
    title: '',
    timeLabel: '',
    description: '',
    priority: 'low',
    completed: false,
})

const priorityLabel = {
    high: 'High priority',
    medium: 'Medium priority',
    low: 'Low priority',
}

const formatTimeLabel = (event) => {
    const opts = { hour: 'numeric', minute: '2-digit' }
    const dateOpts = { weekday: 'short', month: 'short', day: 'numeric' }

    if (!event.start) return ''

    const startDate = event.start.toLocaleDateString(undefined, dateOpts)
    const startTime = event.start.toLocaleTimeString(undefined, opts)

    if (!event.end) return `${startDate} · ${startTime}`

    const endTime = event.end.toLocaleTimeString(undefined, opts)
    return `${startDate} · ${startTime} – ${endTime}`
}

const showTooltip = (mouseEvent, calEvent) => {
    const padding = 14
    tooltip.value = {
        visible: true,
        x: mouseEvent.clientX + padding,
        y: mouseEvent.clientY + padding,
        title: calEvent.title,
        timeLabel: formatTimeLabel(calEvent),
        description: calEvent.extendedProps.description || '',
        priority: calEvent.extendedProps.priority || 'low',
        completed: !!calEvent.extendedProps.completed,
    }
}

const moveTooltip = (mouseEvent) => {
    if (!tooltip.value.visible) return
    const padding = 14
    tooltip.value.x = mouseEvent.clientX + padding
    tooltip.value.y = mouseEvent.clientY + padding
}

const hideTooltip = () => {
    tooltip.value.visible = false
}

const handleEventMount = (info) => {
    info.el.addEventListener('mouseenter', (e) => showTooltip(e, info.event))
    info.el.addEventListener('mousemove', moveTooltip)
    info.el.addEventListener('mouseleave', hideTooltip)
}

const calendarOptions = computed(() => ({
    plugins: [dayGridPlugin, timeGridPlugin, listPlugin, interactionPlugin],
    initialView: 'dayGridMonth',
    headerToolbar: false, 
    events: events,
    height: '100%',
    dayMaxEvents: 3,
    firstDay: 1,
    nowIndicator: true,
    eventDisplay: 'block',
    datesSet: updateTitle,
    eventDidMount: handleEventMount,
    
    eventTimeFormat: {
        hour: 'numeric',
        minute: '2-digit',
        meridiem: 'short',
        hour12: true,
    },
    
    slotLabelFormat: {
        hour: 'numeric',
        minute: '2-digit',
        meridiem: 'short',
        hour12: true,
    },
}))
</script>

<template>
    <Head title="Calendar" />

    <AuthenticatedLayout>
        <Header title="Calendar" />
        <div class="cal-page">
            <!-- Outlook-style toolbar -->
            <div class="cal-toolbar">
                <div class="cal-toolbar__left">
                    <button class="cal-btn cal-btn--today" @click="goToday">Today</button>

                    <div class="cal-nav">
                        <button class="cal-icon-btn" @click="goPrev" aria-label="Previous">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                <path d="M10 3L5 8L10 13" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                        <button class="cal-icon-btn" @click="goNext" aria-label="Next">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                <path d="M6 3L11 8L6 13" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    </div>

                    <h1 class="cal-title">{{ currentTitle }}</h1>
                </div>

                <div class="cal-toolbar__right">
                    <div class="cal-view-switch">
                        <button
                            v-for="opt in viewOptions"
                            :key="opt.key"
                            class="cal-view-btn"
                            :class="{ 'cal-view-btn--active': currentView === opt.key }"
                            @click="switchView(opt.key)"
                        >
                            {{ opt.label }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- Calendar surface -->
            <div class="cal-surface">
                <FullCalendar ref="calendarRef" :options="calendarOptions" />
            </div>

            <!-- Custom hover tooltip -->
            <Transition name="cal-tooltip-fade">
                <div
                    v-if="tooltip.visible"
                    class="cal-tooltip"
                    :class="`cal-tooltip--${tooltip.priority}`"
                    :style="{ left: tooltip.x + 'px', top: tooltip.y + 'px' }"
                >
                    <div class="cal-tooltip__title">{{ tooltip.title }}</div>
                    <div class="cal-tooltip__time">{{ tooltip.timeLabel }}</div>
                    <div v-if="tooltip.description" class="cal-tooltip__desc">{{ tooltip.description }}</div>
                    <div class="cal-tooltip__footer">
                        <span class="cal-tooltip__priority">{{ priorityLabel[tooltip.priority] }}</span>
                        <span v-if="tooltip.completed" class="cal-tooltip__completed">✓ Completed</span>
                    </div>
                </div>
            </Transition>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.cal-page {
    display: flex;
    flex-direction: column;
    height: calc(100vh - 4rem);
    background: #FAF9F8;
    font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, 'Helvetica Neue', Arial, sans-serif;
}

/* ---------- Toolbar ---------- */
.cal-toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 0.75rem;
    padding: 0.85rem 1.5rem;
    background: #FFFFFF;
    border-bottom: 1px solid #EDEBE9;
}

.cal-toolbar__left {
    display: flex;
    align-items: center;
    gap: 0.9rem;
}

.cal-btn {
    border: 1px solid #C8C6C4;
    background: #FFFFFF;
    color: #201F1E;
    font-size: 0.85rem;
    font-weight: 600;
    padding: 0.4rem 0.9rem;
    border-radius: 4px;
    cursor: pointer;
    transition: background 0.12s ease, border-color 0.12s ease;
}
.cal-btn:hover {
    background: #F3F2F1;
    border-color: #0078D4;
}

.cal-nav {
    display: flex;
    gap: 0.25rem;
}

.cal-icon-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 30px;
    height: 30px;
    border: none;
    background: transparent;
    color: #201F1E;
    border-radius: 4px;
    cursor: pointer;
    transition: background 0.12s ease;
}
.cal-icon-btn:hover {
    background: #F3F2F1;
}

.cal-title {
    font-size: 1.15rem;
    font-weight: 600;
    color: #201F1E;
    margin: 0;
    white-space: nowrap;
}

.cal-toolbar__right {
    display: flex;
    align-items: center;
}

.cal-view-switch {
    display: flex;
    border: 1px solid #C8C6C4;
    border-radius: 4px;
    overflow: hidden;
}

.cal-view-btn {
    border: none;
    background: #FFFFFF;
    color: #605E5C;
    font-size: 0.82rem;
    font-weight: 600;
    padding: 0.4rem 0.85rem;
    cursor: pointer;
    border-right: 1px solid #EDEBE9;
    transition: background 0.12s ease, color 0.12s ease;
}
.cal-view-btn:last-child {
    border-right: none;
}
.cal-view-btn:hover {
    background: #F3F2F1;
}
.cal-view-btn--active {
    background: #E8F1FB;
    color: #0078D4;
}

/* ---------- Calendar surface ---------- */
.cal-surface {
    flex: 1;
    min-height: 0;
    padding: 1rem 1.5rem 1.5rem;
}

/* ---------- FullCalendar deep overrides (Outlook-ish grid) ---------- */
.cal-surface :deep(.fc) {
    height: 100%;
    font-family: inherit;
    --fc-border-color: #EDEBE9;
    --fc-today-bg-color: #F3F9FD;
    --fc-page-bg-color: #FFFFFF;
    --fc-neutral-bg-color: #FAF9F8;
}

.cal-surface :deep(.fc-scrollgrid) {
    border: 1px solid #EDEBE9;
    border-radius: 6px;
    overflow: hidden;
    background: #FFFFFF;
}

.cal-surface :deep(.fc-col-header-cell) {
    background: #FAF9F8;
    padding: 0.55rem 0;
}

.cal-surface :deep(.fc-col-header-cell-cushion) {
    color: #605E5C;
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: 0.03em;
    text-transform: uppercase;
    text-decoration: none;
}

.cal-surface :deep(.fc-daygrid-day-number) {
    color: #201F1E;
    font-size: 0.82rem;
    font-weight: 500;
    padding: 0.4rem 0.55rem;
    text-decoration: none;
}

/* Today: small filled circle behind the date number, Outlook-style */
.cal-surface :deep(.fc-day-today .fc-daygrid-day-number) {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: #0078D4;
    color: #FFFFFF;
    font-weight: 700;
    border-radius: 50%;
    width: 22px;
    height: 22px;
    padding: 0;
    margin: 0.3rem;
}

.cal-surface :deep(.fc-daygrid-day-frame) {
    min-height: 92px;
}

.cal-surface :deep(.fc-daygrid-day.fc-day-other) {
    background: #FCFCFB;
}
.cal-surface :deep(.fc-daygrid-day.fc-day-other .fc-daygrid-day-number) {
    color: #C8C6C4;
}

/* Event pills */
.cal-surface :deep(.fc-daygrid-event) {
    border-left-width: 3px;
    border-left-style: solid;
    border-top: none;
    border-right: none;
    border-bottom: none;
    border-radius: 3px;
    padding: 1px 6px;
    font-size: 0.76rem;
    font-weight: 600;
    margin-top: 2px;
    box-shadow: none;
}
.cal-surface :deep(.fc-daygrid-event:hover) {
    filter: brightness(0.97);
}

.cal-surface :deep(.fc-timegrid-event) {
    border-left-width: 3px;
    border-radius: 4px;
    font-size: 0.78rem;
    font-weight: 600;
    box-shadow: none;
}

.cal-surface :deep(.fc-list-event-dot) {
    display: none;
}

.cal-surface :deep(.fc-more-link) {
    color: #0078D4;
    font-weight: 600;
    font-size: 0.75rem;
}

.cal-surface :deep(.fc-timegrid-slot-label) {
    color: #605E5C;
    font-size: 0.75rem;
}

.cal-surface :deep(.fc-timegrid-now-indicator-line) {
    border-color: #D13438;
}
.cal-surface :deep(.fc-timegrid-now-indicator-arrow) {
    border-color: #D13438;
    color: #D13438;
}

/* ---------- Tooltip (hover card) ---------- */
.cal-tooltip {
    position: fixed;
    z-index: 60;
    max-width: 260px;
    background: #FFFFFF;
    border: 1px solid #EDEBE9;
    border-left: 4px solid #0078D4;
    border-radius: 6px;
    box-shadow: 0 4px 14px rgba(0, 0, 0, 0.12);
    padding: 0.7rem 0.85rem;
    pointer-events: none;
    font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, 'Helvetica Neue', Arial, sans-serif;
}
.cal-tooltip--high    { border-left-color: #0078D4; }
.cal-tooltip--medium  { border-left-color: #C19C00; }
.cal-tooltip--low     { border-left-color: #D13438; }

.cal-tooltip__title {
    font-size: 0.88rem;
    font-weight: 700;
    color: #201F1E;
    margin-bottom: 0.2rem;
    line-height: 1.3;
}

.cal-tooltip__time {
    font-size: 0.76rem;
    color: #605E5C;
    margin-bottom: 0.35rem;
}

.cal-tooltip__desc {
    font-size: 0.78rem;
    color: #3B3A39;
    line-height: 1.4;
    margin-bottom: 0.4rem;
    white-space: pre-wrap;
}

.cal-tooltip__footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.5rem;
    font-size: 0.72rem;
    font-weight: 600;
}

.cal-tooltip__priority {
    color: #605E5C;
}

.cal-tooltip__completed {
    color: #107C10;
}

.cal-tooltip-fade-enter-active {
    transition: opacity 0.1s ease;
}
.cal-tooltip-fade-leave-active {
    transition: opacity 0.08s ease;
}
.cal-tooltip-fade-enter-from,
.cal-tooltip-fade-leave-to {
    opacity: 0;
}

/* Mobile: stack toolbar sections */
@media (max-width: 640px) {
    .cal-toolbar {
        flex-direction: column;
        align-items: stretch;
    }
    .cal-toolbar__left,
    .cal-toolbar__right {
        justify-content: space-between;
    }
}
</style>