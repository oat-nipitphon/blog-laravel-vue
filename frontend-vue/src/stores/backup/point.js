import { defineStore } from 'pinia'
import { useRouter } from 'vue-router'
import Swal from 'sweetalert2'

export const usePointStore = defineStore('pointStore', {
    state: () => ({
        points: null,
        errors: {}
    }),
    actions: {

        async getUserPoint($userID) {

        }

    }
});