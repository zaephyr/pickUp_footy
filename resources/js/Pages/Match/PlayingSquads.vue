<template>
    <div>
        <div @click="open = !open">
            <span class="inline-flex rounded-md ">
                <button
                    type="button"
                    class="inline-flex justify-between w-64 items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150"
                >
                    <span>Team {{ teamName }}</span>

                    <svg
                        class="ml-2 -mr-0.5 h-4 w-4"
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                            clip-rule="evenodd"
                        />
                    </svg>
                </button>
            </span>
        </div>

        <!-- Full Screen Dropdown Overlay -->

        <transition
            enter-active-class="transition ease-out duration-200"
            enter-class="transform opacity-0 scale-95"
            enter-to-class="transform opacity-100 scale-100"
            leave-active-class="transition ease-in duration-75"
            leave-class="transform opacity-100 scale-100"
            leave-to-class="transform opacity-0 scale-95"
        >
            <div
                v-show="open"
                class="z-50 mt-2 rounded-md shadow-lg"
                @click="tab == 'subs' ? (open = false) : false"
            >
                <div class="rounded-md ring-1 ring-black ring-opacity-5">
                    <div
                        v-for="player in team"
                        :key="player.id"
                        class="p-2 text-center cursor-pointer"
                        @click="selectPlayer(player)"
                    >
                        {{ player.name }}
                    </div>
                </div>
            </div>
        </transition>
        <div
            class="rounded-md ring-1 ring-black ring-opacity-5 bg-white"
            v-for="(player, index) in selectedPlayer"
            :key="index"
        >
            <div class="p-2 flex justify-between">
                {{ player.name }}
                <span v-if="tab == 'subs'" class="text-end text-red-300"
                    >out</span
                >
                <span
                    v-else-if="tab == 'goal' && index === 0"
                    class="text-start text-green-300"
                    >Goal!</span
                >
                <span
                    v-else-if="tab == 'goal' && index > 0"
                    class="text-start text-red-300"
                    >Assist!</span
                >
            </div>
            <div class="p-2 flex justify-between" v-if="tab == 'subs'">
                <span class="text-start text-green-300">in</span
                >{{ substitute[index].name }}
            </div>
        </div>
    </div>
</template>

<script>
import JetDropdown from "@/Jetstream/Dropdown";

export default {
    components: {
        JetDropdown
    },
    props: {
        team: Array,
        teamName: String,
        tab: String,
        substitute: {
            default: () => null
        }
    },
    data() {
        return {
            selectedPlayer: [],
            open: false
        };
    },
    watch: {
        open(newVal) {
            let result = newVal ? this.teamName : "";

            this.$emit("selectedTeam", result);
        }
    },
    methods: {
        selectPlayer(value) {
            if (this.activeTab === "subs") {
                if (this.substitute.length > this.selectedPlayer.length) {
                    this.selectedPlayer.push(value);
                } else if (this.substitute.length === 1) {
                    this.selectedPlayer[0] = value;
                }
            } else {
                if (this.selectedPlayer.length < 2) {
                    this.selectedPlayer.push(value);
                }
            }
        },
        emitData() {
            this.$emit("selectedPlayers", this.selectedPlayer);
        }
    }
};
</script>

<style></style>
