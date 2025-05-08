import { defineStore } from 'pinia'
import Swal from 'sweetalert2'

export const useAuthStore = defineStore('authStore', {
    state: () => ({
        users: null,
        errors: {},
    }),
    actions: {
        
        async storeApiRegister (apiRouter, formData) {

            const result = await Swal.fire({
                title: '',
                text: '',
                icon: '',
                showCancelButton: true,
                cancelButtonColor: '#d33',
                cancelButtonText: 'Cancel',
                showConfirmButton: true,
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'Confirm',
            })

            if (!result.isConfirmed) {
                Swal.close
                return
            }

            try {

                const ref = await fetch(`/api/${apiRouter}`, {
                    method: 'post',
                    body: JSON.stringify(formData)
                })

                const data = await res.json()

                if (!res.ok) {
                    console.log('store register false', res.data)
                }

                this.users = data.register_user
                console.log('store register success.', this.users)

            } catch (error) {
                console.error('store register function error', error)
            }

        },

    },
})