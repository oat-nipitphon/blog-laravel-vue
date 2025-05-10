import { defineStore } from 'pinia'
import { useRouter } from 'vue-router'
import Swal from 'sweetalert2'

export const useAuthStore = defineStore('authStore', {
    state: () => ({
        userStatus: null,
        users: null,
        errors: {},
    }),
    actions: {

        async storeAPIStartAuth() {

            try {
                const token = localStorage.getItem('token');
                if (!token) {
                    console.log('store api start auth token false');
                    return;
                }

                const res = await fetch(`/api/user`, {
                    method: "GET",
                    headers: {
                        "Content-Type": "multipart/form-data",
                        authorization: `Bearer ${token}`
                    },
                });

                if (!res.ok) {
                    console.log('store api start auth false', res);
                }

                const data = await res.json();
                this.users = data;

            } catch (error) {
                console.error('store api start auth function error', error);
            }

        },

        async storeAPIRegister(apiRouter, formData) {

            const result = await Swal.fire({
                title: 'Confirm!',
                text: 'confirm new account.',
                icon: 'warning',
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

                const res = await fetch(`/api/${apiRouter}`, {
                    method: 'post',
                    body: JSON.stringify(formData)
                })

                if (!res.ok) {
                    Swal.fire({
                        title: 'Register false.',
                        icon: 'error',
                        showCancelButton: true,
                        cancelButtonText: 'OK'
                    }).then(() => {
                        this.errors = data.error;
                    });

                }

                const data = await res.json();
                Swal.fire({
                    title: 'Successfully.',
                    icon: 'success',
                    timer: 1200,
                }).then(() => {
                    this.users = data.register_user;
                });


            } catch (error) {
                console.error('store register function error', error)
            }

        },

        async storeAPILogin(apiRouter, form) {
            try {

                const res = await fetch(`/api/${apiRouter}`, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify(form)
                });

                const data = await res.json();

                if (!res.ok) {
                    console.log('store login false', res);
                }

                localStorage.setItem('token', data.token);
                this.router.push({ name: 'HomeView' })


            } catch (error) {
                console.error('store login function error', error);
            }
        },

        async storeAPILogout() {

            const token = localStorage.getItem('token')

            if (!token) {
                console.error('token false.')
                return
            }

            try {
                const res = await fetch(`/api/logout`, {
                    method: "POST",
                    headers: {
                        "Content-Type": "multipart/form-data",
                        authorization: `Bearer ${localStorage.getItem('token')}`
                    }
                })

                if (!res.ok) {
                    Swal.fire({
                        title: 'Logout false.',
                        icon: 'error',
                        showCancelButton: true,
                        cancelButtonText: 'Close'
                    });
                    return;
                }

                Swal.fire({
                    title: 'Logout successfully.',
                    icon: 'success',
                    timer: 1200,
                }).then(() => {
                    this.users = null;
                    this.errors = {};
                    localStorage.removeItem('token');
                    this.router.push({ name: 'IndexView' })
                });

            } catch (error) {
                console.error('store logout function error', error);
            }
        }

    },
})