<template>
    <app-layout>
        <div class="pb-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="flex h-16 w-56 mx-auto pt-4 mb-4 justify-around">
                    <!-- Logo -->
                    <div class=" space-x-8 sm:-my-px sm:ml-10 sm:flex">
                        <div
                            :class="classesTab(activeTab == 'subs')"
                            @click="activeTab = 'subs'"
                        >
                            Subs
                        </div>
                    </div>

                    <!-- Navigation Links -->
                    <div class=" space-x-8 sm:-my-px sm:ml-10 sm:flex">
                        <div
                            :class="classesTab(activeTab == 'goal')"
                            @click="activeTab = 'goal'"
                        >
                            Goal!
                        </div>
                    </div>
                </div>
                <div
                    class="flex flex-col items-center mb-4"
                    v-if="
                        $page.props.hasRole.key != 'member' &&
                            playingTeamsForm.playingGreen.length > 0
                    "
                    :key="activeTab"
                >
                    <!-- playing teams -->
                    <playing-squads
                        v-if="showGreen"
                        :team="playingTeamsForm.playingGreen"
                        :teamName="'Green'"
                        :substitute="substituteGreen"
                        :tab="activeTab"
                        @selectedTeam="getSelectedTeam"
                        @selectedPlayers="getGreenPlayers"
                    />

                    <playing-squads
                        v-if="showOrange"
                        class="mt-4"
                        :team="playingTeamsForm.playingOrange"
                        :teamName="'Orange'"
                        :substitute="substituteOrange"
                        :tab="activeTab"
                        @selectedTeam="getSelectedTeam"
                        @selectedPlayers="getOrangePlayers"
                    />
                </div>
            </div>

            <jet-dialog-modal
                :show="startingLineup"
                @close="startingLineup = false"
            >
                <template #title>
                    <div class="flex flex-col">
                        <span class="font-semibold"
                            >Pick starting players
                        </span>
                        <span class="text-gray-400 text-xs"
                            >Select benched players</span
                        >
                    </div>
                </template>

                <template #content>
                    <div class="flex flex-col items-center">
                        <div>
                            <div class="font-bold text-green-600 text-center">
                                Team Green
                            </div>
                            <div
                                v-for="playerGreen in squadGreen"
                                :key="playerGreen.id"
                                class=" mt-1 text-center px-4 border border-gray-300 rounded-lg cursor-pointer selection-item"
                                :class="{
                                    'bg-green-200':
                                        pickedGreen == playerGreen.id
                                }"
                                @click="pickedGreen = playerGreen.id"
                            >
                                {{ playerGreen.name }}
                            </div>
                        </div>
                        <div class="mt-8">
                            <div class="font-bold text-yellow-600 text-center">
                                Team Orange
                            </div>
                            <div
                                class=" mt-1 text-center px-4 border border-gray-300 rounded-lg cursor-pointer selection-item"
                                v-for="playerOrange in squadOrange"
                                :key="playerOrange.id"
                                :class="{
                                    'bg-yellow-200':
                                        pickedOrange == playerOrange.id
                                }"
                                @click="pickedOrange = playerOrange.id"
                            >
                                {{ playerOrange.name }}
                            </div>
                        </div>
                    </div>
                </template>

                <template #footer>
                    <jet-secondary-button
                        @click.native="startingLineup = false"
                    >
                        Nevermind
                    </jet-secondary-button>

                    <jet-button
                        class="ml-2 mt-6 sm:mt-0"
                        @click.native="confirmStartingPlayers"
                        :class="{ 'opacity-25': playingTeamsForm.processing }"
                        :disabled="playingTeamsForm.processing"
                    >
                        Confirm starting players
                    </jet-button>
                </template>
            </jet-dialog-modal>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import JetDropdown from "@/Jetstream/Dropdown";
import JetNavLink from "@/Jetstream/NavLink";
import JetDialogModal from "@/Jetstream/DialogModal";
import JetButton from "@/Jetstream/Button";
import JetSecondaryButton from "@/Jetstream/SecondaryButton";
import PlayingSquads from "./PlayingSquads.vue";

export default {
    components: {
        AppLayout,
        JetDropdown,
        JetNavLink,
        JetDialogModal,
        JetButton,
        JetSecondaryButton,
        PlayingSquads
    },
    props: {
        squadGreen: Array,
        squadOrange: Array
    },
    data() {
        return {
            activeTab: "subs",
            startingLineup: true,
            pickedGreen: "",
            pickedOrange: "",
            substituteGreen: [],
            substituteOrange: [],
            showGreen: true,
            showOrange: true,
            selectedTeam: null,
            playingTeamsForm: this.$inertia.form({
                playingOrange: "",
                playingGreen: ""
            })
        };
    },

    methods: {
        classesTab(val) {
            return val
                ? "inline-flex items-center px-1 pt-1 border-b-2 border-indigo-400 text-sm font-medium leading-5 text-gray-900 focus:outline-none focus:border-indigo-700 transition duration-150 ease-in-out"
                : "inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out";
        },
        hideTabIf(value) {
            if (this.activeTab === "goal") {
                return this.selectedTeam === value ? false : true;
            } else {
                return true;
            }
        },
        getSelectedTeam(value) {
            this.selectedTeam = value;

            this.showGreen = this.hideTabIf("Orange");
            this.showOrange = this.hideTabIf("Green");
        },
        getGreenPlayers(value) {
            if (this.activeTab === "subs") {
                value.forEach((sub, i) => {
                    let index = this.playingTeamsForm.playingOrange.findIndex(
                        player => sub.id === player.id
                    );
                    this.playingTeamsForm.playingOrange
                        .splice(1, index)
                        .push(this.substituteGreen[i]);
                    this.substituteGreen[i] = sub;
                });
            }
        },
        confirmStartingPlayers() {
            this.playingTeamsForm.playingOrange = this.squadOrange.filter(
                player => player.id != this.pickedOrange
            );
            this.playingTeamsForm.playingGreen = this.squadGreen.filter(
                player => player.id != this.pickedGreen
            );

            this.substituteGreen.push(
                this.squadGreen.find(player => player.id == this.pickedGreen)
            );
            this.substituteOrange.push(
                this.squadOrange.find(player => player.id == this.pickedOrange)
            );

            this.startingLineup = false;
        }
    }
};
</script>
