<x-filament-widgets::widget>
    <div
        class="pos-dashboard-timebar pos-timebar"
        x-data="{
            now: new Date(),
            tick: null,
            init() {
                this.tick = setInterval(() => {
                    this.now = new Date()
                }, 1000)
            },
            get hour() {
                return this.now.getHours()
            },
            get isDay() {
                return this.hour >= 6 && this.hour < 18
            },
            get period() {
                if (this.hour < 5) return 'Late night'
                if (this.hour < 12) return 'Morning'
                if (this.hour < 17) return 'Afternoon'
                if (this.hour < 21) return 'Evening'

                return 'Night'
            },
            get time() {
                return this.now.toLocaleTimeString([], {
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit',
                })
            },
            get date() {
                return this.now.toLocaleDateString([], {
                    month: 'long',
                    day: 'numeric',
                    year: 'numeric',
                })
            },
            get day() {
                return this.now.toLocaleDateString([], {
                    weekday: 'long',
                })
            },
        }"
        x-bind:class="isDay ? 'is-day' : 'is-night'"
        aria-live="polite"
    >
        <div class="pos-timebar-orbit" aria-hidden="true">
            <span class="pos-timebar-sun"></span>
            <span class="pos-timebar-moon"></span>
            <span class="pos-timebar-star pos-timebar-star-one"></span>
            <span class="pos-timebar-star pos-timebar-star-two"></span>
        </div>

        <div class="pos-timebar-copy">
            <span class="pos-timebar-period" x-text="period"></span>
            <strong x-text="time"></strong>
        </div>

        <div class="pos-timebar-meta">
            <span x-text="day"></span>
            <span x-text="date"></span>
        </div>
    </div>
</x-filament-widgets::widget>
