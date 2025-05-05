<template>
    <!--
      This example requires updating your template:
  
      ```
      <html class="h-full bg-white">
      <body class="h-full">
      ```
    -->
    <div class="flex min-h-full flex-1 flex-col justify-center px-6 py-12 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-sm">
            <img class="mx-auto h-10 w-auto"
                src="https://tailwindcss.com/plus-assets/img/logos/mark.svg?color=indigo&shade=600"
                alt="Your Company" />
            <h2 class="mt-10 text-center text-2xl/9 font-bold tracking-tight text-gray-900">Sign in to your account</h2>
        </div>

        <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
            <form class="space-y-6" @submit.prevent="onLogin(form)">
                <div>
                    <BaseLabel for-id="emailUsername" text="email or username" />
                    <div class="mt-2">
                        <BaseInput 
                            id="emailUsername"
                            type="text",
                            placeholder="email or username"
                            v-model="form.emailUsername"
                        />
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between">
                        <BaseLabel for-id="emailUsername" text="email or username" />
                    </div>
                    <div class="mt-2">
                        <BaseInput 
                            id="password"
                            type="password",
                            placeholder="password"
                            v-model="form.password"
                        />
                    </div>
                </div>

                <div>
                    <button type="submit"
                        class="flex w-full justify-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm/6 font-semibold text-white shadow-xs hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Sign
                        in</button>
                </div>
            </form>
            <div class="grid grid--cols-2">
                <div class="text-sm">
                            <RouterLink class="font-semibold text-indigo-600 hover:text-indigo-500"
                                :to="{ name: 'ForgotPassword' }"
                            >Forgot password?</RouterLink>
                        </div>
            <p class="mt-10 text-center text-sm/6 text-gray-500">
                Not a member?
                {{ ' ' }}
                <RouterLink class="font-semibold text-indigo-600 hover:text-indigo-500"
                    :to="{ name: 'RegisterView' }"
                >create account. ?</RouterLink>
            </p>
            </div>
        </div>
    </div>
</template>
<script setup>
import { ref } from 'vue'
import { RouterLink } from 'vue-router'
import BaseInput from './BaseInput.vue';
import BaseLabel from './BaseLabel.vue';
import { useAuthStore } from '@/stores/auth'

const { apiStoreLogin } = useAuthStore()

const form = ref({
    emailUsername: '',
    password: ''
})

const onLogin = async () => {
    console.log('on login', form)
    await apiStoreLogin('login', form)
}

</script>