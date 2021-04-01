<script>
import FullCalendar from '@fullcalendar/vue'
import dayGridPlugin from '@fullcalendar/daygrid'
import interactionPlugin from '@fullcalendar/interaction'
import timeGridPlugin from '@fullcalendar/timegrid'
import momentPlugin from '@fullcalendar/moment'

export default {
    components: {
        FullCalendar, // make the <FullCalendar> tag available

    },

    data() {
        return {

            eventGuid: 0,

            newEvent: {
                    event_name: "",
                    start_date: "",
                    end_date: ""
                    // defaultAllDay: true
                },
            addingMode: true,
            indexToUpdate: "",

            calendarOptions: {
                plugins: [
                dayGridPlugin,
                timeGridPlugin,
                interactionPlugin, // needed for dateClick
                momentPlugin
                ],

                events: [],

                headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },

                // titleFormat: 'MMMM D, YYYY', // you can now use moment format strings!

                initialView: 'dayGridMonth',
                editable: true,
                selectable: true,
                selectMirror: true,
                dayMaxEvents: true,
                weekends: true,
                select: this.handleDateSelect,
                eventClick: this.showEvent,
                eventsSet: this.handleEvents,
                defaultAllDay: true,
                eventDrop: this.eventDrop,

            },
        }
    },

    created() {
        this.getEvents();
    },

    methods: {

        eventDrop(info){

            // console.log(info);
            this.indexToUpdate = info.event.id; //変更したいeventのidを指定
            this.newEvent = {
                event_name: info.event.title, //タイトルはそのまま代入し直す
                start_date: info.event.startStr, //drop先の日付に変更
                end_date: info.event.endStr, //drop先の日付に変更
            }

            axios.put("/calendar/" + this.indexToUpdate, {
            ...this.newEvent
            });

        },

        createEventId() {
            return String(this.eventGuid++)
        },


        handleDateSelect(selectInfo) {
        let title = prompt('Please enter a new title for your event')
        let calendarApi = selectInfo.view.calendar

        calendarApi.unselect() // clear date selection

        if (title) {
            this.newEvent = {
            event_name: title,
            start_date: selectInfo.startStr,
            end_date: selectInfo.endStr,
            }

            this.addNewEvent()
        }


        },

        // handleEventClick(clickInfo) {
        //     if (confirm(`Are you sure you want to delete the event '${clickInfo.event.title}'`)) {
        //         clickInfo.event.remove()
        //     }
        // },

        handleEvents(events) {
            this.currentEvents = events
        },

        getEvents() {
            axios
                .get("/calendar")
                .then(resp => (this.calendarOptions.events = resp.data.data))
                .catch(err => console.log(err.response.data));
        },

        addNewEvent() {
            axios
                .post("/calendar",
                {...this.newEvent}
                    // this.newEvent = {
                    //     event_name,
                    //     start_date,
                    //     end_date: new Date(+end_date+60*60*24*1000)
                    //     }
                )
                .then(resp => {
                this.getEvents(); // update our list of events
                this.resetForm(); // clear newEvent properties (e.g. title, start_date and end_date)
                })
                .catch(err =>
                console.log("Unable to add new event!", err.response.data)
                );
        },

        showEvent(arg) {
            this.addingMode = false;
            const { id, title, start, end } = this.calendarOptions.events.find(
                event => event.id === +arg.event.id
            );
            this.indexToUpdate = id;
            this.newEvent = {
                event_name: title,
                start_date: start,
                end_date: end
            };
        },

        updateEvent() {
            axios
                .put("/calendar/" + this.indexToUpdate, {
                ...this.newEvent
                })
                .then(resp => {
                this.resetForm();
                this.getEvents();
                this.addingMode = !this.addingMode;
                })
                .catch(err =>
                console.log("Unable to update event!", err.response.data)
                );
        },

        deleteEvent() {
            axios
                .delete("/calendar/" + this.indexToUpdate)
                .then(resp => {
                this.resetForm();
                this.getEvents();
                this.addingMode = !this.addingMode;
                })
                .catch(err =>
                console.log("Unable to delete event!", err.response.data)
                );
        },

        resetForm() {
            Object.keys(this.newEvent).forEach(key => {
                return (this.newEvent[key] = "");
            });
        },


    },

    watch: {
    indexToUpdate() {
      return this.indexToUpdate;
    }
  }

}
</script>
<template>
    <div>
        <div class="container">
            <form @submit.prevent>
              <div class="form-group">
                <label for="event_name">Event Name</label>
                <input
                    type="text"
                    id="event_name"
                    class="form-control"
                    v-model="newEvent.event_name"
                    required
                >
              </div>
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="start_date">Start Date</label>
                    <input
                        type="datetime-local"
                        id="start_date"
                        class="form-control"
                        v-model="newEvent.start_date"
                        required
                    >
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="end_date">End Date</label>
                    <input
                        type="datetime-local"
                        id="end_date"
                        class="form-control"
                        v-model="newEvent.end_date"
                        required
                    >
                  </div>
                </div>
                <div class="col-md-6 mb-4" v-if="addingMode">
                  <button class="btn btn-sm btn-primary" @click="this.addNewEvent">Save Event</button>
                </div>
                <template v-else>
                  <div class="col-md-6 mb-4">
                    <button class="btn btn-sm btn-success" @click="updateEvent">Update</button>
                    <button class="btn btn-sm btn-danger" @click="deleteEvent">Delete</button>
                    <button class="btn btn-sm btn-secondary" @click="addingMode = !addingMode">Cancel</button>
                  </div>
                </template>
              </div>
            </form>
        </div>

        <div class="container">
            <FullCalendar :options="calendarOptions" @eventClick="showEvent"/>
        </div>
    </div>
</template>
