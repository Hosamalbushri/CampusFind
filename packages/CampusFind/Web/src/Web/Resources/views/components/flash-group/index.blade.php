<v-flash-group ref="flashes"></v-flash-group>

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-flash-group-template"
    >
        <transition-group
            tag="div"
            name="flash-group"
            enter-from-class="-translate-y-6 opacity-0 scale-95"
            enter-active-class="transform transition duration-300 ease-out"
            enter-to-class="translate-y-0 opacity-100 scale-100"
            leave-from-class="translate-y-0 opacity-100 scale-100"
            leave-active-class="transform transition duration-250 ease-in"
            leave-to-class="-translate-y-6 opacity-0 scale-95"
            class="fixed top-4 sm:top-5 ltr:right-4 rtl:left-4 sm:ltr:right-6 sm:rtl:left-6 z-[10003] flex flex-col items-end gap-3 font-cairo max-w-md w-[calc(100%-2rem)] sm:w-full pointer-events-none"
        >
            <x-web::flash-group.item />
        </transition-group>
    </script>

    <script type="module">
        app.component('v-flash-group', {
            template: '#v-flash-group-template',

            data() {
                return {
                    uid: 0,
                    flashes: [],
                };
            },

            created() {
                @foreach (['success', 'warning', 'error', 'info'] as $key)
                    @if (session()->has($key))
                        this.flashes.push({
                            type: '{{ $key }}',
                            message: "{!! addslashes(session($key)) !!}",
                            uid: this.uid++,
                        });
                    @endif
                @endforeach

                this.registerGlobalEvents();
            },

            methods: {
                add(flash) {
                    flash.uid = this.uid++;
                    this.flashes.push(flash);
                },

                remove(flash) {
                    const index = this.flashes.indexOf(flash);
                    if (index !== -1) {
                        this.flashes.splice(index, 1);
                    }
                },

                registerGlobalEvents() {
                    if (this.$emitter) {
                        this.$emitter.on('add-flash', this.add);
                    }
                },
            },
        });
    </script>
@endPushOnce
