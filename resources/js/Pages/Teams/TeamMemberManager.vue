<template>
    <div>
        <div v-if="userPermissions.canAddTeamMembers">
            <jet-section-border />

            <!-- Add new Match Event -->
            <jet-form-section @submitted="createNewMatch">
                <template #title>
                    New Match
                </template>

                <template #description>
                    Add new match event for your team.
                </template>

                <template #form>
                    <div class="col-span-6">
                        <div class="max-w-xl text-sm text-gray-600">
                            Please provide starting date and time for this
                            event.
                        </div>
                    </div>

                    <!-- Event Date -->
                    <div class="col-span-6 sm:col-span-4">
                        <jet-label for="event" value="Event Date" />
                        <jet-input
                            id="event_date"
                            type="datetime-local"
                            class="mt-1 block w-full"
                            v-model="addNewMatchEvent.date"
                        />
                        <jet-input-error
                            :message="addNewMatchEvent.errors.date"
                            class="mt-2"
                        />
                    </div>
                </template>

                <template #actions>
                    <label class="flex items-center mr-auto">
                        <jet-checkbox
                            name="attend"
                            v-model="addNewMatchEvent.attend"
                        />
                        <span class="ml-2 text-sm text-gray-600"
                            >Attend this event</span
                        >
                    </label>
                    <jet-action-message
                        :on="addNewMatchEvent.recentlySuccessful"
                        class="mr-3"
                    >
                        Added.
                    </jet-action-message>

                    <jet-button
                        :class="{
                            'opacity-25': addNewMatchEvent.processing
                        }"
                        :disabled="addNewMatchEvent.processing"
                    >
                        Add
                    </jet-button>
                </template>
            </jet-form-section>
        </div>

        <div v-if="userPermissions.canAddTeamMembers">
            <jet-section-border />

            <!-- Add Team Member -->
            <jet-form-section @submitted="addTeamMember">
                <template #title>
                    Add Team Member
                </template>

                <template #description>
                    Add a new team member to your team, allowing them to
                    collaborate with you.
                </template>

                <template #form>
                    <div class="col-span-6">
                        <div class="max-w-xl text-sm text-gray-600">
                            Please provide the email address of the person you
                            would like to add to this team.
                        </div>
                    </div>

                    <!-- Member Email -->
                    <div class="col-span-6 sm:col-span-4">
                        <jet-label for="email" value="Email" />
                        <jet-input
                            id="email"
                            type="email"
                            class="mt-1 block w-full"
                            v-model="addTeamMemberForm.email"
                        />
                        <jet-input-error
                            :message="addTeamMemberForm.errors.email"
                            class="mt-2"
                        />
                    </div>

                    <!-- Role -->
                    <div
                        class="col-span-6 lg:col-span-4"
                        v-if="availableRoles.length > 0"
                    >
                        <jet-label for="roles" value="Role" />
                        <jet-input-error
                            :message="addTeamMemberForm.errors.role"
                            class="mt-2"
                        />

                        <div
                            class="relative z-0 mt-1 border border-gray-200 rounded-lg cursor-pointer"
                        >
                            <button
                                type="button"
                                class="relative px-4 py-3 inline-flex w-full rounded-lg focus:z-10 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue"
                                :class="{
                                    'border-t border-gray-200 rounded-t-none':
                                        i > 0,
                                    'rounded-b-none':
                                        i !=
                                        Object.keys(availableRoles).length - 1
                                }"
                                @click="addTeamMemberForm.role = role.key"
                                v-for="(role, i) in availableRoles"
                                :key="role.key"
                            >
                                <div
                                    :class="{
                                        'opacity-50':
                                            addTeamMemberForm.role &&
                                            addTeamMemberForm.role != role.key
                                    }"
                                >
                                    <!-- Role Name -->
                                    <div class="flex items-center">
                                        <div
                                            class="text-sm text-gray-600"
                                            :class="{
                                                'font-semibold':
                                                    addTeamMemberForm.role ==
                                                    role.key
                                            }"
                                        >
                                            {{ role.name }}
                                        </div>

                                        <svg
                                            v-if="
                                                addTeamMemberForm.role ==
                                                    role.key
                                            "
                                            class="ml-2 h-5 w-5 text-green-400"
                                            fill="none"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                            ></path>
                                        </svg>
                                    </div>

                                    <!-- Role Description -->
                                    <div class="mt-2 text-xs text-gray-600">
                                        {{ role.description }}
                                    </div>
                                </div>
                            </button>
                        </div>
                    </div>
                </template>

                <template #actions>
                    <jet-action-message
                        :on="addTeamMemberForm.recentlySuccessful"
                        class="mr-3"
                    >
                        Added.
                    </jet-action-message>

                    <jet-button
                        :class="{ 'opacity-25': addTeamMemberForm.processing }"
                        :disabled="addTeamMemberForm.processing"
                    >
                        Add
                    </jet-button>
                </template>
            </jet-form-section>
        </div>

        <div
            v-if="
                team.team_invitations.length > 0 &&
                    userPermissions.canAddTeamMembers
            "
        >
            <jet-section-border />

            <!-- Team Member Invitations -->
            <jet-action-section class="mt-10 sm:mt-0">
                <template #title>
                    Pending Team Invitations
                </template>

                <template #description>
                    These people have been invited to your team and have been
                    sent an invitation email. They may join the team by
                    accepting the email invitation.
                </template>

                <!-- Pending Team Member Invitation List -->
                <template #content>
                    <div class="space-y-6">
                        <div
                            class="flex items-center justify-between"
                            v-for="invitation in team.team_invitations"
                            :key="invitation.id"
                        >
                            <div class="text-gray-600">
                                {{ invitation.email }}
                            </div>

                            <div class="flex items-center">
                                <!-- Cancel Team Invitation -->
                                <button
                                    class="cursor-pointer ml-6 text-sm text-red-500 focus:outline-none"
                                    @click="cancelTeamInvitation(invitation)"
                                    v-if="userPermissions.canRemoveTeamMembers"
                                >
                                    Cancel
                                </button>
                            </div>
                        </div>
                    </div>
                </template>
            </jet-action-section>
        </div>

        <div v-if="userPermissions.canAddTeamMembers">
            <jet-section-border />

            <!-- Add Team Member -->
            <jet-form-section
                @submitted="createTeamMember"
                class="mt-10 sm:mt-0"
            >
                <template #title>
                    Create Team Member
                </template>

                <template #description>
                    Create a new team member, allowing them to collaborate with
                    you.
                </template>

                <template #form>
                    <div class="col-span-6">
                        <div class="max-w-xl text-sm text-gray-600">
                            Please provide name, email address and password of
                            the person you would like to add to this team.
                        </div>
                    </div>

                    <!-- Member Name -->
                    <div class="col-span-6 sm:col-span-4">
                        <jet-label for="name" value="Name" />
                        <jet-input
                            id="create_name"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="createTeamMemberForm.name"
                        />
                        <jet-input-error
                            :message="createTeamMemberForm.errors.name"
                            class="mt-2"
                        />
                    </div>

                    <!-- Member Email -->
                    <div class="col-span-6 sm:col-span-4">
                        <jet-label for="email" value="Email" />
                        <jet-input
                            id="create_email"
                            type="email"
                            class="mt-1 block w-full"
                            v-model="createTeamMemberForm.email"
                        />
                        <jet-input-error
                            :message="createTeamMemberForm.errors.email"
                            class="mt-2"
                        />
                    </div>

                    <div class="col-span-6 sm:col-span-4">
                        <jet-label for="create_password" value="Password" />
                        <jet-input
                            id="create_password"
                            type="password"
                            class="mt-1 block w-full"
                            v-model="createTeamMemberForm.password"
                            required
                            autocomplete="new-password"
                        />
                    </div>

                    <div class="col-span-6 sm:col-span-4">
                        <jet-label
                            for="create_password_confirmation"
                            value="Confirm Password"
                        />
                        <jet-input
                            id="create_password_confirmation"
                            type="password"
                            class="mt-1 block w-full"
                            v-model="createTeamMemberForm.password_confirmation"
                            required
                            autocomplete="new-password"
                        />
                    </div>

                    <!-- Role -->
                    <div
                        class="col-span-6 lg:col-span-4"
                        v-if="availableRoles.length > 0"
                    >
                        <jet-label for="roles" value="Role" />
                        <jet-input-error
                            :message="createTeamMemberForm.errors.role"
                            class="mt-2"
                        />

                        <div
                            class="relative z-0 mt-1 border border-gray-200 rounded-lg cursor-pointer"
                        >
                            <button
                                type="button"
                                class="relative px-4 py-3 inline-flex w-full rounded-lg focus:z-10 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue"
                                :class="{
                                    'border-t border-gray-200 rounded-t-none':
                                        i > 0,
                                    'rounded-b-none':
                                        i !=
                                        Object.keys(availableRoles).length - 1
                                }"
                                @click="createTeamMemberForm.role = role.key"
                                v-for="(role, i) in availableRoles"
                                :key="role.key"
                            >
                                <div
                                    :class="{
                                        'opacity-50':
                                            createTeamMemberForm.role &&
                                            createTeamMemberForm.role !=
                                                role.key
                                    }"
                                >
                                    <!-- Role Name -->
                                    <div class="flex items-center">
                                        <div
                                            class="text-sm text-gray-600"
                                            :class="{
                                                'font-semibold':
                                                    createTeamMemberForm.role ==
                                                    role.key
                                            }"
                                        >
                                            {{ role.name }}
                                        </div>

                                        <svg
                                            v-if="
                                                createTeamMemberForm.role ==
                                                    role.key
                                            "
                                            class="ml-2 h-5 w-5 text-green-400"
                                            fill="none"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                            ></path>
                                        </svg>
                                    </div>

                                    <!-- Role Description -->
                                    <div class="mt-2 text-xs text-gray-600">
                                        {{ role.description }}
                                    </div>
                                </div>
                            </button>
                        </div>
                    </div>
                </template>

                <template #actions>
                    <jet-action-message
                        :on="createTeamMemberForm.recentlySuccessful"
                        class="mr-3"
                    >
                        Created.
                    </jet-action-message>

                    <jet-button
                        :class="{
                            'opacity-25': createTeamMemberForm.processing
                        }"
                        :disabled="createTeamMemberForm.processing"
                    >
                        Create
                    </jet-button>
                </template>
            </jet-form-section>
        </div>

        <div v-if="team.users.length > 0">
            <jet-section-border />

            <!-- Manage Team Members -->
            <jet-action-section class="mt-10 sm:mt-0">
                <template #title>
                    Team Members
                </template>

                <template #description>
                    All of the people that are part of this team.
                </template>

                <!-- Team Member List -->
                <template #content>
                    <div class="space-y-6">
                        <div
                            class="flex items-center justify-between"
                            v-for="user in team.users"
                            :key="user.id"
                        >
                            <div class="flex items-center">
                                <img
                                    class="w-8 h-8 rounded-full"
                                    :src="user.profile_photo_url"
                                    :alt="user.name"
                                />
                                <div class="ml-4">{{ user.name }}</div>
                            </div>

                            <div class="flex items-center">
                                <!-- Manage Team Member Role -->
                                <button
                                    class="ml-2 text-sm text-gray-400 underline"
                                    @click="manageRole(user)"
                                    v-if="
                                        userPermissions.canAddTeamMembers &&
                                            availableRoles.length
                                    "
                                >
                                    {{ displayableRole(user.membership.role) }}
                                </button>

                                <div
                                    class="ml-2 text-sm text-gray-400"
                                    v-else-if="availableRoles.length"
                                >
                                    {{ displayableRole(user.membership.role) }}
                                </div>

                                <!-- Leave Team -->
                                <button
                                    class="cursor-pointer ml-6 text-sm text-red-500"
                                    @click="confirmLeavingTeam"
                                    v-if="$page.props.user.id === user.id"
                                >
                                    Leave
                                </button>

                                <!-- Remove Team Member -->
                                <button
                                    class="cursor-pointer ml-6 text-sm text-red-500"
                                    @click="confirmTeamMemberRemoval(user)"
                                    v-if="userPermissions.canRemoveTeamMembers"
                                >
                                    Remove
                                </button>
                            </div>
                        </div>
                    </div>
                </template>
            </jet-action-section>
        </div>

        <!-- Role Management Modal -->
        <jet-dialog-modal
            :show="currentlyManagingRole"
            @close="currentlyManagingRole = false"
        >
            <template #title>
                Manage Role
            </template>

            <template #content>
                <div v-if="managingRoleFor">
                    <div
                        class="relative z-0 mt-1 border border-gray-200 rounded-lg cursor-pointer"
                    >
                        <button
                            type="button"
                            class="relative px-4 py-3 inline-flex w-full rounded-lg focus:z-10 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue"
                            :class="{
                                'border-t border-gray-200 rounded-t-none':
                                    i > 0,
                                'rounded-b-none':
                                    i !== Object.keys(availableRoles).length - 1
                            }"
                            @click="updateRoleForm.role = role.key"
                            v-for="(role, i) in availableRoles"
                            :key="role.key"
                        >
                            <div
                                :class="{
                                    'opacity-50':
                                        updateRoleForm.role &&
                                        updateRoleForm.role !== role.key
                                }"
                            >
                                <!-- Role Name -->
                                <div class="flex items-center">
                                    <div
                                        class="text-sm text-gray-600"
                                        :class="{
                                            'font-semibold':
                                                updateRoleForm.role === role.key
                                        }"
                                    >
                                        {{ role.name }}
                                    </div>

                                    <svg
                                        v-if="updateRoleForm.role === role.key"
                                        class="ml-2 h-5 w-5 text-green-400"
                                        fill="none"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                        ></path>
                                    </svg>
                                </div>

                                <!-- Role Description -->
                                <div class="mt-2 text-xs text-gray-600">
                                    {{ role.description }}
                                </div>
                            </div>
                        </button>
                    </div>
                </div>
            </template>

            <template #footer>
                <jet-secondary-button
                    @click.native="currentlyManagingRole = false"
                >
                    Nevermind
                </jet-secondary-button>

                <jet-button
                    class="ml-2"
                    @click.native="updateRole"
                    :class="{ 'opacity-25': updateRoleForm.processing }"
                    :disabled="updateRoleForm.processing"
                >
                    Save
                </jet-button>
            </template>
        </jet-dialog-modal>

        <!-- Leave Team Confirmation Modal -->
        <jet-confirmation-modal
            :show="confirmingLeavingTeam"
            @close="confirmingLeavingTeam = false"
        >
            <template #title>
                Leave Team
            </template>

            <template #content>
                Are you sure you would like to leave this team?
            </template>

            <template #footer>
                <jet-secondary-button
                    @click.native="confirmingLeavingTeam = false"
                >
                    Nevermind
                </jet-secondary-button>

                <jet-danger-button
                    class="ml-2"
                    @click.native="leaveTeam"
                    :class="{ 'opacity-25': leaveTeamForm.processing }"
                    :disabled="leaveTeamForm.processing"
                >
                    Leave
                </jet-danger-button>
            </template>
        </jet-confirmation-modal>

        <!-- Remove Team Member Confirmation Modal -->
        <jet-confirmation-modal
            :show="teamMemberBeingRemoved"
            @close="teamMemberBeingRemoved = null"
        >
            <template #title>
                Remove Team Member
            </template>

            <template #content>
                Are you sure you would like to remove this person from the team?
            </template>

            <template #footer>
                <jet-secondary-button
                    @click.native="teamMemberBeingRemoved = null"
                >
                    Nevermind
                </jet-secondary-button>

                <jet-danger-button
                    class="ml-2"
                    @click.native="removeTeamMember"
                    :class="{ 'opacity-25': removeTeamMemberForm.processing }"
                    :disabled="removeTeamMemberForm.processing"
                >
                    Remove
                </jet-danger-button>
            </template>
        </jet-confirmation-modal>
    </div>
</template>

<script>
import JetActionMessage from "@/Jetstream/ActionMessage";
import JetActionSection from "@/Jetstream/ActionSection";
import JetButton from "@/Jetstream/Button";
import JetConfirmationModal from "@/Jetstream/ConfirmationModal";
import JetDangerButton from "@/Jetstream/DangerButton";
import JetDialogModal from "@/Jetstream/DialogModal";
import JetFormSection from "@/Jetstream/FormSection";
import JetInput from "@/Jetstream/Input";
import JetInputError from "@/Jetstream/InputError";
import JetLabel from "@/Jetstream/Label";
import JetSecondaryButton from "@/Jetstream/SecondaryButton";
import JetSectionBorder from "@/Jetstream/SectionBorder";
import JetCheckbox from "@/Jetstream/Checkbox";

export default {
    components: {
        JetActionMessage,
        JetActionSection,
        JetButton,
        JetConfirmationModal,
        JetDangerButton,
        JetDialogModal,
        JetFormSection,
        JetInput,
        JetInputError,
        JetLabel,
        JetSecondaryButton,
        JetSectionBorder,
        JetCheckbox
    },

    props: ["team", "availableRoles", "userPermissions"],

    data() {
        return {
            addNewMatchEvent: this.$inertia.form({
                date: "",
                attend: false
            }),
            addTeamMemberForm: this.$inertia.form({
                email: "",
                role: null
            }),
            createTeamMemberForm: this.$inertia.form({
                name: "",
                email: "",
                password: "",
                password_confirmation: "",
                role: null
            }),

            updateRoleForm: this.$inertia.form({
                role: null
            }),

            leaveTeamForm: this.$inertia.form(),
            removeTeamMemberForm: this.$inertia.form(),

            currentlyManagingRole: false,
            managingRoleFor: null,
            confirmingLeavingTeam: false,
            teamMemberBeingRemoved: null
        };
    },
    methods: {
        createNewMatch() {
            this.addNewMatchEvent.post(
                route("team.create.new_match", this.team),
                {
                    errorBag: "createNewMatch",
                    preserveScroll: true,
                    onSuccess: () => {
                        this.addNewMatchEvent.reset();
                    }
                }
            );
        },
        addTeamMember() {
            this.addTeamMemberForm.post(
                route("team-members.store", this.team),
                {
                    errorBag: "addTeamMember",
                    preserveScroll: true,
                    onSuccess: () => this.addTeamMemberForm.reset()
                }
            );
        },
        createTeamMember() {
            this.createTeamMemberForm.post(
                route("team.create.new_user", this.team),
                {
                    errorBag: "createTeamMember",
                    preserveScroll: true,
                    onSuccess: () => this.createTeamMemberForm.reset()
                }
            );
        },

        cancelTeamInvitation(invitation) {
            this.$inertia.delete(
                route("team-invitations.destroy", invitation),
                {
                    preserveScroll: true
                }
            );
        },

        manageRole(teamMember) {
            this.managingRoleFor = teamMember;
            this.updateRoleForm.role = teamMember.membership.role;
            this.currentlyManagingRole = true;
        },

        updateRole() {
            this.updateRoleForm.put(
                route("team-members.update", [this.team, this.managingRoleFor]),
                {
                    preserveScroll: true,
                    onSuccess: () => (this.currentlyManagingRole = false)
                }
            );
        },

        confirmLeavingTeam() {
            this.confirmingLeavingTeam = true;
        },

        leaveTeam() {
            this.leaveTeamForm.delete(
                route("team-members.destroy", [
                    this.team,
                    this.$page.props.user
                ])
            );
        },

        confirmTeamMemberRemoval(teamMember) {
            this.teamMemberBeingRemoved = teamMember;
        },

        removeTeamMember() {
            this.removeTeamMemberForm.delete(
                route("team-members.destroy", [
                    this.team,
                    this.teamMemberBeingRemoved
                ]),
                {
                    errorBag: "removeTeamMember",
                    preserveScroll: true,
                    preserveState: true,
                    onSuccess: () => (this.teamMemberBeingRemoved = null)
                }
            );
        },

        displayableRole(role) {
            return this.availableRoles.find(r => r.key === role).name;
        }
    }
};
</script>
