<script setup>
import axiosAPI from '@/services/axiosAPI'
import { ref, onMounted, computed } from 'vue'
import { RouterLink } from 'vue-router'
import { storeToRefs } from 'pinia'
import { useAuthStore } from '@/stores/auth'
import { usePostStore } from '@/stores/post'
import imageFileBasic from '@/assets/icon/keyboard.jpg'
import QuickViewProfileCard from '@/components/QuickViewProfileCard.vue';


const authStore = useAuthStore()
const { storeUser } = storeToRefs(authStore)

const props = defineProps({
  title: String,
  content: String,
})

const {
  apiGetPosts,
  apiStorePost,
  apiDeletePost,
  apiPostPopLike,
  apiPostPopDisLike,
} = usePostStore()

const userID = ref(authStore.storeUser?.user_login?.id || null)
const posts = ref([])
const selectedPostContent = ref([])
const selectedUserProfile = ref([])
const likeCount = ref(null)
const disLikeCount = ref(null)
const isLoading = ref(false)

const testImageProfileNull = async () => {
  const response = await fetch(imageFileBasic)
  const blob = await response.blob()
  const file = new File([blob], 'default-image.jpg', { type: 'image/jpeg' })
  // console.log('function test image profile null', file);
  return file, blob
}

// Compute enriched data for posts
const enrichedPosts = computed(() =>
  posts.value.map(post => {
    const likeCount = post.postPopularity.filter(
      pop => pop.status === 'Like'
    ).length
    const disLikeCount = post.postPopularity.filter(
      pop => pop.status === 'DisLike'
    ).length
    const userLiked = post.postPopularity.some(
      pop => pop.userID === userID.value && pop.status === 'Like'
    )
    const userDisliked = post.postPopularity.some(
      pop => pop.userID === userID.value && pop.status === 'DisLike'
    )
    return {
      ...post,
      likeCount,
      disLikeCount,
      userLiked,
      userDisliked,
    }
  })
)

onMounted(async () => {
  posts.value = await apiGetPosts()
})

const handleLike = async (postID, userID) => {
  if (isLoading.value) return
  isLoading.value = true
  await apiPostPopLike(userID, postID, 'Like');
  const updatedPosts = await apiGetPosts()
  posts.value = updatedPosts
  isLoading.value = false
}

const handleDislike = async (postID, userID) => {
  if (isLoading.value) return
  isLoading.value = true
  await apiPostPopDisLike(userID, postID, 'DisLike');
  const updatedPosts = await apiGetPosts()
  posts.value = updatedPosts
  isLoading.value = false
}


const modalValuePostContent = content => {
  selectedPostContent.value = content
}

const formatDateTime = (dateTime) => {
  if (!dateTime) return '-';

  const date = new Date(dateTime);

  const year = date.getFullYear() + 543; // แปลงเป็น พ.ศ.
  const month = date.getMonth(); // 0-11
  const day = date.getDate();

  const hour = date.getHours().toString().padStart(2, '0');
  const minute = date.getMinutes().toString().padStart(2, '0');
  const second = date.getSeconds().toString().padStart(2, '0');

  const thaiMonths = [
    'มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน',
    'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'
  ];

  return `${day} ${thaiMonths[month]} ${year} เวลา ${hour}:${minute}:${second} น.`;
};

const onModalShowUserProfile = userProfile => {
  selectedUserProfile.value = userProfile
}

const onFollowers = async (postUserID, authUserID) => {
  try {
    const res = await axiosAPI.post(
      `/api/followers/${postUserID}/${authUserID}`,
      {}, // ถ้าไม่มี body ให้ส่งเป็น {} ไป
      {
        headers: {
          Authorization: `Bearer ${localStorage.getItem('token')}`,
          'Content-Type': 'application/json', // หรือเอาออกไปเลยก็ได้ถ้าไม่มี file upload
        }
      }
    );
    if (res.error) {
      console.log('api followers error', res);
    }
    posts.value = await apiGetPosts()
  } catch (error) {
    console.error('function followers error', error);
  }
}

const onPopLike = async (postUserID, authUserID) => {
  try {
    const res = await axiosAPI.post(
      `/api/pop_like/${postUserID}/${authUserID}`,
      {}, // ถ้าไม่มี body ให้ส่งเป็น {} ไป
      {
        headers: {
          Authorization: `Bearer ${localStorage.getItem('token')}`,
          'Content-Type': 'application/json', // หรือเอาออกไปเลยก็ได้ถ้าไม่มี file upload
        }
      }
    );
    if (res.error) {
      console.log('api pop like error', res);
    }

    console.log('api pop like success', res);

    posts.value = await apiGetPosts()

  } catch (error) {
    console.error('function pop like error', error);
  }
}

const isFollowing = (followersList, userId) => {
  return followersList?.some(f => f.followers_user_id === userId && f.status_followers === 'true');
};
</script>

<template>
  <div class="w-3/4 mx-auto">
    <div class="mt-5 flex justify-end">
      <RouterLink class="btn btn-sm btn-primary mr-10" :to="{
        name: 'CreatePostNewView'
      }">
        Create post
      </RouterLink>
    </div>

    <div v-if="enrichedPosts.length > 0" class="bg-white max-w-3/4">

      <div class="lg:w-full md:w-md sm:w-sm lg:h-full md:h-md sm:h-sm shadow-md rounded-md p-5 mt-5 mb-5"
        v-for="(post, index) in enrichedPosts" :key="index">

        <div class="grid grid-cols-2">

          <div>
            <figure class=" bg-white border-gray-200 md:rounded-es-lg dark:bg-gray-800 dark:border-gray-700">
              <figcaption class="flex items-start justify-start ml-5 mt-3">

                <!-- image profile user create post -->
                <div v-for="(image, index) in post.userImage" :key="index">
                  <div v-if="image.imageData !== null">
                    <QuickViewProfileCard :imageProfile="'data:image/png;base64,' + image.imageData" />
                  </div>
                  <div v-else>
                    <img class="size-10 rounded-full" src="../assets/icon/icon-user-default.png" alt="" />
                  </div>
                </div>

                <!-- Full name user create and Event Popularity Profile -->
                <div class="space-y-0.5 font-medium dark:text-white text-left rtl:text-right ms-3">
                  <div>{{ post.userProfile?.fullName }}</div>
                  <div class="text-sm text-gray-500 dark:text-gray-400">
                    <div class="grid grid-cols-2">

                      <!-- Followers Profile create post  -->
                      <div class="flex justify-center">
                        <div class="grid grid-cols-2">
                          <div class="flex justify-center items-center">
                            <button class="btn btn-sm"
                              @click="onFollowers(post.userProfile?.id, authStore.storeUser.user_login?.id)">
                              <div class="grid grid-cols-3">
                                <div class="flex justify-center">
                                  <svg
                                    v-if="post.userFollowersProfile.some(followers => followers.followers_user_id === authStore.storeUser.user_login.id && followers.status_followers === 'true')"
                                    xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                    class="bi bi-bell-fill text-yellow-300 w-[30px] h-[30px]" viewBox="0 0 16 16">
                                    <path
                                      d="M8 16a2 2 0 0 0 2-2H6a2 2 0 0 0 2 2m.995-14.901a1 1 0 1 0-1.99 0A5 5 0 0 0 3 6c0 1.098-.5 6-2 7h14c-1.5-1-2-5.902-2-7 0-2.42-1.72-4.44-4.005-4.901" />
                                  </svg>
                                  <svg v-else xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                    class="bi bi-bell-fill w-[30px] h-[30px]" viewBox="0 0 16 16">
                                    <path
                                      d="M8 16a2 2 0 0 0 2-2H6a2 2 0 0 0 2 2m.995-14.901a1 1 0 1 0-1.99 0A5 5 0 0 0 3 6c0 1.098-.5 6-2 7h14c-1.5-1-2-5.902-2-7 0-2.42-1.72-4.44-4.005-4.901" />
                                  </svg>
                                </div>
                                <div class="flex justify-center">
                                  <p class="m-auto mt-auto text-gray-700 text-md-2xl">
                                    {{post.userFollowersProfile.filter(followers => followers.status_followers ===
                                      'true').length || 0}}
                                  </p>
                                </div>
                                <div class="flex justify-center m-auto">ติดตาม</div>
                              </div>
                            </button>
                          </div>
                        </div>
                      </div>

                      <!-- Popularity Profile create post Dis Like -->
                      <div class="flex justify-center">
                        <div class="grid grid-cols-2">
                          <div class="flex justify-center items-center">
                            <button class="btn btn-sm"
                              @click="onPopLike(post.userProfile?.id, authStore.storeUser.user_login?.id)">
                              <div class="grid grid-cols-3">
                                <div class="flex justify-center">
                                  <svg
                                    v-if="post.userPopularityProfiles.some(pop => pop.user_id_pop === authStore.storeUser.user_login?.id && pop.status_pop === 'true')"
                                    xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                    class="bi bi-heart-fill text-red-600 w-[30px] h-[30px]" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd"
                                      d="M8 1.314C12.438-3.248 23.534 4.735 8 15-7.534 4.736 3.562-3.248 8 1.314" />
                                  </svg>
                                  <svg v-else xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                    class="bi bi-heart-fill w-[30px] h-[30px]" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd"
                                      d="M8 1.314C12.438-3.248 23.534 4.735 8 15-7.534 4.736 3.562-3.248 8 1.314" />
                                  </svg>
                                </div>
                                <div class="flex justify-center">
                                  <p class="m-auto mt-auto text-gray-700 text-md-2xl">
                                    {{post.userPopularityProfiles.filter(popLike => popLike.status_pop ===
                                      'true').length ||
                                      0}}
                                  </p>
                                </div>
                                <div class="flex justify-center m-auto">ถูกใจ</div>
                              </div>
                            </button>
                          </div>
                        </div>
                      </div>

                    </div>
                  </div>
                </div>

              </figcaption>
            </figure>
          </div>


          <div class="flex justify-end">

            <div class="grid grid-rows-2">
              <!-- Show date time create post or update and recover -->
              <label class="font-semibold text-sm text-gray-700 mt-2 ml-5">
                สร้างโพสต์ วันที่ {{ formatDateTime(post.updatedAt) }}
              </label>

              <!-- Post Menu dropdown Event -->
              <div v-if="post.userID === authStore.storeUser.user_login.id">
                <div class="dropdown mr-5 mt-3">
                  <img class="size-6 mr-5 mt-3" src="../assets/icon/sliders.svg" alt="SettingPost"
                    data-bs-toggle="dropdown" aria-expanded="false" />
                  <ul class="dropdown-menu">
                    <li>
                      <button class="dropdown-item" type="submit" @click="apiStorePost(post.id)">
                        <label for="Event-Store" class="text-sm ml-2 text-gray-900">
                          จัดเก็บ
                        </label>
                      </button>
                    </li>
                    <li>
                      <RouterLink :to="{
                        name: 'EditPostView',
                        params: { id: post.id },
                      }" class="dropdown-item">
                        <label for="Event-Post-Edit" class="text-sm ml-2 text-gray-900">
                          แก้ไข
                        </label>
                      </RouterLink>
                    </li>
                    <li>
                      <button @click="apiDeletePost(post.id)" class="dropdown-item">
                        <label for="Event-Post-Delete" class="text-sm ml-2 text-gray-900">
                          ลบ
                        </label>
                      </button>
                    </li>
                  </ul>
                </div>
              </div>
            </div>

          </div>
        </div>

        <!-- Post Image -->
        <div class="w-full p-4 m-auto flex justify-center" v-for="postImage in post.postImage" :key="postImage.id">
          <img class="h-60 w-full object-cover"
            :src="'data:image/png/jpg;base64,' + postImage.imageData" alt="Sunset in the mountains" />
        </div>

        <!-- report post box -->
        <div class="p-2 ml-5 mr-5">
          <blockquote class="max-w-2xl mx-auto mb-4 text-gray-500 lg:mb-8 dark:text-gray-400">

            <div class="grid grid-cols-2">
                  <!-- Post PopLike -->
                  <div class="flex justify-center items-center">
                    <button
                      v-if="post.postPopularity.some(pop => pop.userID === authStore.storeUser.user_login.id && pop.status === 'Like')"
                      :disabled="isLoading" type="button" class="btn btn-lg m-auto"
                      @click="handleLike(post.id, authStore.storeUser.user_login?.id)">
                      <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                        class="w-[30px] h-[30px] bi bi-hand-thumbs-up-fill text-blue-600" viewBox="0 0 16 16">
                        <path
                          d="M6.956 1.745C7.021.81 7.908.087 8.864.325l.261.066c.463.116.874.456 1.012.965.22.816.533 2.511.062 4.51a10 10 0 0 1 .443-.051c.713-.065 1.669-.072 2.516.21.518.173.994.681 1.2 1.273.184.532.16 1.162-.234 1.733q.086.18.138.363c.077.27.113.567.113.856s-.036.586-.113.856c-.039.135-.09.273-.16.404.169.387.107.819-.003 1.148a3.2 3.2 0 0 1-.488.901c.054.152.076.312.076.465 0 .305-.089.625-.253.912C13.1 15.522 12.437 16 11.5 16H8c-.605 0-1.07-.081-1.466-.218a4.8 4.8 0 0 1-.97-.484l-.048-.03c-.504-.307-.999-.609-2.068-.722C2.682 14.464 2 13.846 2 13V9c0-.85.685-1.432 1.357-1.615.849-.232 1.574-.787 2.132-1.41.56-.627.914-1.28 1.039-1.639.199-.575.356-1.539.428-2.59z" />
                      </svg>
                      {{post.postPopularity.filter(pop => pop.status === 'Like').length || 0}}
                    </button>
                    <button v-else :disabled="isLoading" type="button" class="btn btn-lg m-auto"
                      @click="handleLike(post.id, authStore.storeUser.user_login?.id)">
                      <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                        class="w-[30px] h-[30px] bi bi-hand-thumbs-up" viewBox="0 0 16 16">
                        <path
                          d="M8.864.046C7.908-.193 7.02.53 6.956 1.466c-.072 1.051-.23 2.016-.428 2.59-.125.36-.479 1.013-1.04 1.639-.557.623-1.282 1.178-2.131 1.41C2.685 7.288 2 7.87 2 8.72v4.001c0 .845.682 1.464 1.448 1.545 1.07.114 1.564.415 2.068.723l.048.03c.272.165.578.348.97.484.397.136.861.217 1.466.217h3.5c.937 0 1.599-.477 1.934-1.064a1.86 1.86 0 0 0 .254-.912c0-.152-.023-.312-.077-.464.201-.263.38-.578.488-.901.11-.33.172-.762.004-1.149.069-.13.12-.269.159-.403.077-.27.113-.568.113-.857 0-.288-.036-.585-.113-.856a2 2 0 0 0-.138-.362 1.9 1.9 0 0 0 .234-1.734c-.206-.592-.682-1.1-1.2-1.272-.847-.282-1.803-.276-2.516-.211a10 10 0 0 0-.443.05 9.4 9.4 0 0 0-.062-4.509A1.38 1.38 0 0 0 9.125.111zM11.5 14.721H8c-.51 0-.863-.069-1.14-.164-.281-.097-.506-.228-.776-.393l-.04-.024c-.555-.339-1.198-.731-2.49-.868-.333-.036-.554-.29-.554-.55V8.72c0-.254.226-.543.62-.65 1.095-.3 1.977-.996 2.614-1.708.635-.71 1.064-1.475 1.238-1.978.243-.7.407-1.768.482-2.85.025-.362.36-.594.667-.518l.262.066c.16.04.258.143.288.255a8.34 8.34 0 0 1-.145 4.725.5.5 0 0 0 .595.644l.003-.001.014-.003.058-.014a9 9 0 0 1 1.036-.157c.663-.06 1.457-.054 2.11.164.175.058.45.3.57.65.107.308.087.67-.266 1.022l-.353.353.353.354c.043.043.105.141.154.315.048.167.075.37.075.581 0 .212-.027.414-.075.582-.05.174-.111.272-.154.315l-.353.353.353.354c.047.047.109.177.005.488a2.2 2.2 0 0 1-.505.805l-.353.353.353.354c.006.005.041.05.041.17a.9.9 0 0 1-.121.416c-.165.288-.503.56-1.066.56z" />
                      </svg>
                      {{post.postPopularity.filter(pop => pop.status === 'Like').length || 0}}
                    </button>
                  </div>

                  <!-- Post DisLike -->
                  <div class="flex justify-center items-center">
                    <button
                      v-if="post.postPopularity.some(pop => pop.userID === authStore.storeUser.user_login.id && pop.status === 'DisLike')"
                      :disabled="isLoading" type="button" class="btn btn-lg m-auto"
                      @click="handleDislike(post.id, authStore.storeUser.user_login?.id)">

                      <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                        class="w-[30px] h-[30px] bi bi-hand-thumbs-down-fill text-red-600" viewBox="0 0 16 16">
                        <path
                          d="M6.956 14.534c.065.936.952 1.659 1.908 1.42l.261-.065a1.38 1.38 0 0 0 1.012-.965c.22-.816.533-2.512.062-4.51q.205.03.443.051c.713.065 1.669.071 2.516-.211.518-.173.994-.68 1.2-1.272a1.9 1.9 0 0 0-.234-1.734c.058-.118.103-.242.138-.362.077-.27.113-.568.113-.856 0-.29-.036-.586-.113-.857a2 2 0 0 0-.16-.403c.169-.387.107-.82-.003-1.149a3.2 3.2 0 0 0-.488-.9c.054-.153.076-.313.076-.465a1.86 1.86 0 0 0-.253-.912C13.1.757 12.437.28 11.5.28H8c-.605 0-1.07.08-1.466.217a4.8 4.8 0 0 0-.97.485l-.048.029c-.504.308-.999.61-2.068.723C2.682 1.815 2 2.434 2 3.279v4c0 .851.685 1.433 1.357 1.616.849.232 1.574.787 2.132 1.41.56.626.914 1.28 1.039 1.638.199.575.356 1.54.428 2.591" />
                      </svg>
                      {{post.postPopularity.filter(pop => pop.status === 'DisLike').length || 0}}
                    </button>
                    <button v-else :disabled="isLoading" type="button" class="btn btn-lg m-auto"
                      @click="handleDislike(post.id, authStore.storeUser.user_login?.id)">
                      <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                        class="w-[30px] h-[30px] bi bi-hand-thumbs-down" viewBox="0 0 16 16">
                        <path
                          d="M8.864 15.674c-.956.24-1.843-.484-1.908-1.42-.072-1.05-.23-2.015-.428-2.59-.125-.36-.479-1.012-1.04-1.638-.557-.624-1.282-1.179-2.131-1.41C2.685 8.432 2 7.85 2 7V3c0-.845.682-1.464 1.448-1.546 1.07-.113 1.564-.415 2.068-.723l.048-.029c.272-.166.578-.349.97-.484C6.931.08 7.395 0 8 0h3.5c.937 0 1.599.478 1.934 1.064.164.287.254.607.254.913 0 .152-.023.312-.077.464.201.262.38.577.488.9.11.33.172.762.004 1.15.069.13.12.268.159.403.077.27.113.567.113.856s-.036.586-.113.856c-.035.12-.08.244-.138.363.394.571.418 1.2.234 1.733-.206.592-.682 1.1-1.2 1.272-.847.283-1.803.276-2.516.211a10 10 0 0 1-.443-.05 9.36 9.36 0 0 1-.062 4.51c-.138.508-.55.848-1.012.964zM11.5 1H8c-.51 0-.863.068-1.14.163-.281.097-.506.229-.776.393l-.04.025c-.555.338-1.198.73-2.49.868-.333.035-.554.29-.554.55V7c0 .255.226.543.62.65 1.095.3 1.977.997 2.614 1.709.635.71 1.064 1.475 1.238 1.977.243.7.407 1.768.482 2.85.025.362.36.595.667.518l.262-.065c.16-.04.258-.144.288-.255a8.34 8.34 0 0 0-.145-4.726.5.5 0 0 1 .595-.643h.003l.014.004.058.013a9 9 0 0 0 1.036.157c.663.06 1.457.054 2.11-.163.175-.059.45-.301.57-.651.107-.308.087-.67-.266-1.021L12.793 7l.353-.354c.043-.042.105-.14.154-.315.048-.167.075-.37.075-.581s-.027-.414-.075-.581c-.05-.174-.111-.273-.154-.315l-.353-.354.353-.354c.047-.047.109-.176.005-.488a2.2 2.2 0 0 0-.505-.804l-.353-.354.353-.354c.006-.005.041-.05.041-.17a.9.9 0 0 0-.121-.415C12.4 1.272 12.063 1 11.5 1" />
                      </svg>
                      {{post.postPopularity.filter(pop => pop.status === 'DisLike').length || 0}}
                    </button>
                  </div>
            </div>

            <!-- Show sub content and Event button click show modal all content -->
            <div class="p-2">
              <p class="my-4 post-content" v-html="post.content"></p>
              <button v-if="post.content.length > 200" type="button" class="btn btn-sm text-primary"
                data-bs-toggle="modal" data-bs-target="#modalShowMovePostContents"
                @click="modalValuePostContent(post.content)">
                <p class="flex justify-between text-blue-600 text-sm m-auto">
                  อ่านเพิ่มเติม ...
                </p>
              </button>

              <!-- Modal Show All Content -->
              <div class="modal fade" id="modalShowMovePostContents" tabindex="-1" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title">เนื้อหาทั้งหมด</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                      <p class="text-md text-gray-700" v-html="selectedPostContent"></p>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">
                        ปิด
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>

          </blockquote>
        </div>

      </div>
    </div>

    <!-- show report requert false data null -->
    <div v-else class="flex justify-center mt-5">
      <p class="text-lg font-medium text-red-600">ไม่มีเนื้อหาบทความ</p>
    </div>

  </div>
</template>
<style>
.image-profile-title-post {
  width: 200px;
  height: 120px;
}

.post-content {
  overflow: hidden;
  display: -webkit-box;
  -webkit-line-clamp: 5;
  -webkit-box-orient: vertical;

  line-clamp: 5;
  box-orient: vertical;
  word-break: break-word;
  max-height: 120px;
  white-space: pre-wrap;
  word-wrap: break-word;
  overflow-wrap: break-word;
}
</style>
