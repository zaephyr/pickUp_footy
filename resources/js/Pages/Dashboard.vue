<template>
    <app-layout>
        <template #header class="sm:pl-20 ">
            <div class="flex justify-between" v-if="!!members">
                <div>
                    <span class="text-sm font-thin text-gray-400 "
                        >Next match:</span
                    >
                    <h2
                        class="font-semibold text-xl leading-tight text-gray-600"
                    >
                        {{ matchData.date }}
                    </h2>
                </div>
                <button
                    @click="manageSquads"
                    v-if="$page.props.hasRole.key != 'member'"
                    class="bg-green-500 inline-flex items-center  border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray transition ease-in-out duration-150"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="icon icon-tabler icon-tabler-soccer-field"
                        width="72"
                        height="72"
                        viewBox="0 0 24 24"
                        stroke-width="1.5"
                        stroke="#ffffff"
                        fill="none"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <circle cx="12" cy="12" r="3" />
                        <path d="M3 9h3v6h-3z" />
                        <path d="M18 9h3v6h-3z" />
                        <rect x="3" y="5" width="18" height="14" rx="2" />
                        <line x1="12" y1="5" x2="12" y2="19" />
                    </svg>
                </button>
            </div>
            <div v-else>Dashboard</div>
        </template>

        <div class="py-12" v-if="!!members">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div
                    class="flex justify-center mb-4"
                    v-if="$page.props.hasRole.key != 'member'"
                >
                    <button
                        class="bg-green-500 inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray transition ease-in-out duration-150"
                        @click="applyAttends()"
                    >
                        Apply Attending
                    </button>
                </div>
                <div
                    class="bg-white overflow-hidden shadow-xl sm:rounded-lg flex flex-col items-center pt-6"
                >
                    <div
                        v-for="(member, index) in members"
                        :key="member.id"
                        class="selection-item"
                        :class="[
                            {
                                'mb-4':
                                    index ==
                                    members.map(el => el.attend).lastIndexOf(1),
                                'mt-4':
                                    index ==
                                    members.findIndex(el => el.attend == -1),
                                'w-3/4 sm:w-1/2  md:w-2/5 lg:w-1/4 flex justify-between':
                                    $page.props.hasRole.key != 'member'
                            }
                        ]"
                    >
                        <span
                            :class="{
                                // ja: $page.props.hasRole.key == 'member',
                                'text-green-500': member.attend == 1,
                                'text-red-400': member.attend == -1
                            }"
                            >{{ member.name }}</span
                        >
                        <span
                            class="text-center"
                            v-if="$page.props.hasRole.key != 'member'"
                        >
                            <jet-checkbox
                                name="attend"
                                v-model="attending[index].attendBool"
                            />
                        </span>
                    </div>

                    <div class="mt-4 mb-10 flex justify-around w-4/5 sm:w-1/2">
                        <button
                            class="bg-green-500 inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray transition ease-in-out duration-150"
                            @click="updateAttending(1)"
                        >
                            Pridem
                        </button>
                        <button
                            class="bg-red-400 inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray transition ease-in-out duration-150"
                            @click="updateAttending(-1)"
                        >
                            Ne pridem
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <jet-dialog-modal
            :show="managingSquads"
            @close="managingSquads = false"
        >
            <template #title class="flex justify-between">
                <div class="flex justify-between">
                    <span> Manage Match Squads</span
                    ><span
                        >{{
                            managedSquadsNotSaved.filter(el => el.squad == true)
                                .length
                        }}
                        vs
                        {{
                            managedSquadsNotSaved.filter(
                                el => el.squad == false
                            ).length
                        }}
                    </span>
                </div>
            </template>

            <template #content>
                <div class="flex flex-col items-center">
                    <transition-group name="selection" tag="div">
                        <div
                            v-for="(member, index) in managedSquadsNotSaved"
                            :key="member.id"
                            class=" text-center mt-1 px-4 border border-gray-300 rounded-lg cursor-pointer selection-item"
                            :class="{
                                'mb-4':
                                    index ==
                                    managedSquadsNotSaved
                                        .map(el => el.squad)
                                        .lastIndexOf(true),
                                'text-green-500': member.squad
                            }"
                            @click="toggleSquad(index)"
                        >
                            {{ member.name }}
                        </div>
                    </transition-group>
                </div>
            </template>

            <template #footer>
                <jet-secondary-button @click.native="managingSquads = false">
                    Nevermind
                </jet-secondary-button>

                <jet-button
                    class="ml-2 mt-6 sm:mt-0"
                    @click.native="makeSquadsAndStartMatch"
                    :class="{ 'opacity-25': matchSquadForm.processing }"
                    :disabled="matchSquadForm.processing"
                >
                    Confirm squads & Start Game
                </jet-button>
            </template>
        </jet-dialog-modal>
    </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import JetCheckbox from "@/Jetstream/Checkbox";
import JetDialogModal from "@/Jetstream/DialogModal";
import JetButton from "@/Jetstream/Button";
import JetSecondaryButton from "@/Jetstream/SecondaryButton";

export default {
    components: {
        AppLayout,
        JetCheckbox,
        JetDialogModal,
        JetButton,
        JetSecondaryButton
    },
    props: { members: Array, currentTeamMember: Object, matchData: Object },
    data() {
        return {
            attending: [],
            managingSquads: false,
            matchSquadForm: this.$inertia.form({
                date: ""
            }),
            managedSquadsNotSaved: []
        };
    },
    created() {
        this.cleanData();
        this.matchData.managedSquads.sort((a, b) => b.squad - a.squad);
    },

    methods: {
        applyAttends() {
            let membersToUpdate = this.attending
                .filter(
                    el =>
                        ((el.attend == 0 || el.attend == -1) &&
                            el.attendBool) ||
                        (el.attend == 1 && !el.attendBool)
                )
                .map(el => {
                    let param = {
                        id: el.member_id,
                        attend: el.attendBool ? 1 : -1
                    };
                    return param;
                });
            this.$inertia.put(
                route("dashboard.update.mass"),
                {
                    membersToUpdate
                },
                {
                    preserveScroll: true,
                    onSuccess: () => {
                        this.cleanData();
                    },
                    onError: errors => {
                        console.log(errors);
                    }
                }
            );
        },
        cleanData() {
            this.members.sort((a, b) => b.attend - a.attend);
            this.attending = this.members.map(el => {
                let member = {
                    member_id: el.member_id,
                    attend: el.attend,
                    attendBool: el.attend == 1
                };
                return member;
            });
            this.matchData.managedSquads = this.members
                .filter(el => el.attend == 1)
                .map(el => {
                    console.log(el.squad);
                    el.squad = el.squad ?? false;
                    return el;
                })
                .sort((a, b) => b.squad - a.squad);
        },
        updateAttending(val) {
            if (this.currentTeamMember.attend != val) {
                this.$inertia.put(
                    route("dashboard.update", this.currentTeamMember.id),
                    {
                        attend: val
                    },
                    {
                        preserveScroll: true,
                        onSuccess: () => {
                            this.cleanData();
                        },
                        onError: errors => {
                            console.log(errors);
                        }
                    }
                );
            }
        },
        manageSquads() {
            if (this.matchData.managedSquads.length == 0) {
                this.matchData.managedSquads = this.members
                    .filter(el => el.attend == 1)
                    .map(el => {
                        el.squad = false;
                        return el;
                    });
            }
            //Making deep copy, ie no references
            this.managedSquadsNotSaved = JSON.parse(
                JSON.stringify(this.matchData.managedSquads)
            );

            this.managingSquads = true;
        },
        toggleSquad(key) {
            this.managedSquadsNotSaved[key].squad = !this.managedSquadsNotSaved[
                key
            ].squad;
            this.managedSquadsNotSaved.sort((a, b) => b.squad - a.squad);
        },
        makeSquadsAndStartMatch() {
            this.matchData.managedSquads = this.managedSquadsNotSaved;
            this.$inertia.post(
                route("match.squads", this.matchData.match_id),
                {
                    managedSquads: this.matchData.managedSquads
                },
                {
                    preserveScroll: true,
                    onSuccess: () => {
                        this.cleanData();
                    },
                    onError: errors => {
                        console.log(errors);
                    }
                }
            );
            this.managingSquads = false;
        }
    }
};
</script>

<style>
.selection-move {
    transition: all 1s;
}
</style>
