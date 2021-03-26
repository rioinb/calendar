<script>
import FullCalendar from '@fullcalendar/vue'
import dayGridPlugin from '@fullcalendar/daygrid'
import interactionPlugin from '@fullcalendar/interaction'
import timeGridPlugin from '@fullcalendar/timegrid'


export default {
    components: {
        FullCalendar // make the <FullCalendar> tag available
    },

    data() {
        return {

            eventGuid: 0,

            calendarOptions: {
                plugins: [
                dayGridPlugin,
                timeGridPlugin,
                interactionPlugin // needed for dateClick
                ],
                headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                initialView: 'dayGridMonth',
                editable: true,
                selectable: true,
                selectMirror: true,
                dayMaxEvents: true,
                weekends: true,
                select: this.handleDateSelect,
                eventClick: this.handleEventClick,
                eventsSet: this.handleEvents,
        }}
    },

    methods: {

        // createEventId() {
        //     return String(eventGuid++)
        // },

        handleWeekendsToggle() {
        this.calendarOptions.weekends = !this.calendarOptions.weekends // update a property
        },

        handleDateSelect(selectInfo) {
        let title = prompt('Please enter a new title for your event')
        let calendarApi = selectInfo.view.calendar

        calendarApi.unselect() // clear date selection

        if (title) {
            calendarApi.addEvent({
            // id: createEventId(),
            title,
            start: selectInfo.startStr,
            end: selectInfo.endStr,
            allDay: selectInfo.allDay
            })
        }
        },

        handleEventClick(clickInfo) {
            if (confirm(`Are you sure you want to delete the event '${clickInfo.event.title}'`)) {
                clickInfo.event.remove()
            }
        },

        handleEvents(events) {
            this.currentEvents = events
        },


    },

}
</script>
<template>
  <FullCalendar :options="calendarOptions" />
</template>
