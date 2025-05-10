<template>
    <div class="m-auto">
        <div class="grid grid-cols-2">
            <div class="flex justify-center">
                <button class="btn btn-sm btn-outline-secondary ml-2" @click="openModal('register')">Register</button>
            </div>
            <div class="flex justify-center">
                <button class="btn btn-sm btn-outline-primary ml-2" @click="openModal('login')">Login</button>
            </div>
        </div>

        <div class="m-auto w-full">
            <TransitionRoot as="template" :show="open">
                <Dialog class="fixed inset-0 z-10 flex items-start justify-center mt-20" @close="closeModal">
                    <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0 scale-95"
                        enter-to="opacity-100 scale-100" leave="ease-in duration-200" leave-from="opacity-100 scale-100"
                        leave-to="opacity-0 scale-95">
                        <DialogPanel class="w-full max-w-md bg-white rounded-lg shadow-xl p-6">
                            <DialogTitle as="h3" class="text-lg font-semibold text-gray-900 mb-4">
                                {{ modalType === 'register' ? 'Register' : 'Login' }}
                            </DialogTitle>

                            <form class="space-y-4"
                                @submit.prevent="modalType === 'register' ? onRegister() : onLogin()">
                                <div v-if="modalType === 'register'">
                                    <BaseLabel for-id="email" text="Email" />
                                    <BaseInput id="email" type="text" placeholder="email" v-model="form.email"
                                        @blur="checkEmail" />
                                    <p v-if="modalType === 'register'"
                                        :class="emailError.includes('สามารถ') ? 'text-green-600' : 'text-red-600'"
                                        class="text-sm mt-2">
                                        {{ emailError }}
                                    </p>
                                </div>

                                <div v-if="modalType === 'register'">
                                    <BaseLabel for-id="username" text="Username" />
                                    <BaseInput id="username" type="text" placeholder="username" v-model="form.username"
                                        @blur="checkUsername" />
                                    <p v-if="modalType === 'register'"
                                        :class="usernameError.includes('สามารถ') ? 'text-green-600' : 'text-red-600'"
                                        class="text-sm mt-2">
                                        {{ usernameError }}
                                    </p>
                                </div>


                                <div v-if="modalType === 'login'">
                                    <BaseLabel for-id="email or username" text="Email & Username" />
                                    <BaseInput 
                                        id="emailUsername"
                                        type="text"
                                        placeholder="email or username"
                                        v-model="form.emailUsername"
                                    />
                                </div>

                                <div>
                                    <BaseLabel for-id="password" text="Password" />
                                    <BaseInput id="password" type="password" placeholder="password"
                                        v-model="form.password" />
                                    <div v-if="modalType === 'register'" v-html="passwordCheck"></div>
                                </div>

                                <div v-if="modalType === 'register'">
                                    <BaseLabel for-id="confirmPassword" text="Confirm Password" />
                                    <BaseInput id="confirmPassword" type="password" placeholder="confirm password"
                                        v-model="form.confirmPassword" />
                                    <div v-html="passwordConfirmErrorMessage"></div>
                                </div>

                                <div v-if="modalType === 'register'">
                                    <BaseSelect id="statusID" label="สถานะ" v-model="form.statusID"
                                        :options="userStatus" optionKey="id" optionLabel="name"
                                        placeholder="เลือกสถานะ" />
                                    <p v-if="!form.statusID" class="text-red-600 text-sm mt-2">โปรดเลือกสถานะบัญชี
                                        เข้าใช้งานระบบ.</p>
                                    <p v-else class="text-green-600 text-sm mt-2">เลือกสถานะบัญชีสำเร็จ.</p>
                                </div>

                                <button type="submit"
                                    class="w-full py-2 px-4 bg-indigo-600 text-white rounded-md hover:bg-indigo-500"
                                    :disabled="isSubmitting">
                                    <span v-if="isSubmitting">Loading...</span>
                                    <span v-else>{{ modalType === 'register' ? 'Register' : 'Login' }}</span>
                                </button>
                            </form>

                            <div class="mt-4 flex justify-between text-sm text-gray-500">
                                <RouterLink v-if="modalType === 'login'" @click.prevent="modalType = 'register'" to="#">
                                    Don't have an account? Register
                                </RouterLink>
                                <RouterLink v-else @click.prevent="modalType = 'login'" to="#">
                                    Already have an account? Login
                                </RouterLink>
                            </div>

                            <div class="mt-6 text-right">
                                <button class="px-4 py-2 bg-gray-200 text-gray-900 rounded-md hover:bg-gray-300"
                                    @click="closeModal">Cancel</button>
                            </div>
                        </DialogPanel>
                    </TransitionChild>
                </Dialog>
            </TransitionRoot>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { RouterLink } from 'vue-router'
import axiosAPI from '@/services/axiosAPI'
import BaseInput from '@/components/base/Input.vue'
import BaseLabel from '@/components/base/Label.vue'
import BaseSelect from '@/components/base/Select.vue'
import { useAuthStore } from '@/stores/auth'
import {
    Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot
} from '@headlessui/vue'

const open = ref(false)
const modalType = ref('register')
const userStatus = ref([])
const isSubmitting = ref(false)

const emailError = ref('')
const usernameError = ref('')

const form = reactive({
    email: '',
    username: '',
    emailUsername: '',
    password: '',
    confirmPassword: '',
    statusID: ''
})

const { storeAPIRegister, storeAPILogin } = useAuthStore()

const onRegister = async () => {
    if (form.password !== form.confirmPassword) {
        alert("Passwords do not match")
        return
    }

    isSubmitting.value = true
    try {
        await storeAPIRegister('register', form)
        closeModal()
    } catch (error) {
        console.error(error)
    } finally {
        isSubmitting.value = false
    }
}

const onLogin = async () => {
    isSubmitting.value = true
    try {
        await storeAPILogin('login', form)
        closeModal()
    } catch (error) {
        console.error(error)
    } finally {
        isSubmitting.value = false
    }
}

const getUserStatus = async () => {
    try {
        const res = await axiosAPI.get('/api/get_user_status')
        return res.data.userStatus;
    } catch (error) {
        console.error('Error fetching user status:', error)
        return []
    }
}

const openModal = (type) => {
    modalType.value = type
    open.value = true
}

const closeModal = () => {
    open.value = false
    Object.assign(form, {
        email: '',
        username: '',
        password: '',
        confirmPassword: '',
        statusID: ''
    })
    emailError.value = ''
    usernameError.value = ''
}

const checkEmail = async () => {
    if (!form.email.includes('@')) {
        emailError.value = 'รูปแบบอีเมลไม่ถูกต้อง เช่น example@gmail.com'
        return
    }

    try {
        const response = await axiosAPI.post('/api/register/check_email', { email: form.email })
        emailError.value = response.data.exists
            ? 'อีเมลนี้ถูกใช้งานแล้ว กรุณาใช้อีเมลอื่น'
            : 'อีเมลนี้สามารถใช้งานได้'
    } catch (error) {
        console.error(error)
        emailError.value = 'เกิดข้อผิดพลาดในการตรวจสอบอีเมล'
    }
}

const checkUsername = async () => {
    if (!form.username) {
        usernameError.value = 'โปรดกรอกชื่อผู้ใช้'
        return
    }

    try {
        const response = await axiosAPI.post('/api/register/check_username', { username: form.username })
        usernameError.value = response.data.exists
            ? 'ชื่อผู้ใช้นี้ถูกใช้ไปแล้ว'
            : 'ชื่อผู้ใช้นี้สามารถใช้งานได้'
    } catch (error) {
        console.error(error)
        usernameError.value = 'เกิดข้อผิดพลาดในการตรวจสอบชื่อผู้ใช้'
    }
}

const passwordCheck = computed(() => {
    if (!form.password) {
        return `<p class="text-red-600 text-sm mt-2">โปรดกรอกรหัสผ่าน สำหรับลงทะเบียน.</p>`
    }
    if (form.password.length < 6) {
        return `<p class="text-red-600 text-sm mt-2">รหัสผ่านต้องมีจำนวนมากกว่า 5 ตัว</p>`
    }
    return `<p class="text-green-600 text-sm mt-2">รหัสผ่านถูกต้อง สามารถใช้งานได้.</p>`
})

const passwordConfirmErrorMessage = computed(() => {
    if (!form.confirmPassword) {
        return `<p class="text-red-600 text-sm mt-2">โปรดยืนยันรหัสผ่านอีกครั้ง สำหรับลงทะเบียน.</p>`
    }
    if (form.password !== form.confirmPassword) {
        return `<p class="text-red-600 text-sm mt-2">รหัสผ่านไม่ตรงกัน โปรดตรวจสอบรหัสผ่านของท่านอีกครั้ง</p>`
    }
    return `<p class="text-green-600 text-sm mt-2">รหัสผ่านตรงกัน สามารถใช้งานได้.</p>`
})

onMounted(async () => {
    userStatus.value = await getUserStatus()
})
</script>
