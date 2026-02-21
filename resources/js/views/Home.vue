<template>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <!-- Hero Section -->
        <section
            class="bg-gradient-to-r from-primary-600 to-primary-800 dark:from-primary-800 dark:to-primary-900 text-white py-16"
        >
            <div class="container mx-auto px-4">
                <div class="max-w-3xl mx-auto text-center">
                    <h1 class="text-4xl md:text-5xl font-bold mb-4">
                        Welcome to Laravel Uganda Community
                    </h1>
                    <p class="text-xl text-primary-100 mb-8">
                        Connect, learn, and grow with fellow Laravel Developers
                        in Uganda. Ask questions, share resources, find jobs,
                        and attend events.
                    </p>
                    <div
                        class="flex flex-col sm:flex-row gap-4 justify-center"
                        v-if="!authStore.isAuthenticated"
                    >
                        <router-link
                            to="/register"
                            class="px-6 py-3 bg-white text-primary-600 rounded-lg font-semibold hover:bg-primary-50 transition"
                        >
                            Join the Community
                        </router-link>
                        <router-link
                            to="/login"
                            class="px-6 py-3 border-2 border-white text-white rounded-lg font-semibold hover:bg-white/10 transition"
                        >
                            Sign In
                        </router-link>
                    </div>
                </div>
            </div>
        </section>

        <!-- Stats Section -->
        <section
            class="py-8 bg-white dark:bg-gray-800 border-b dark:border-gray-700"
        >
            <div class="container mx-auto px-4">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                    <div>
                        <div
                            class="text-3xl font-bold text-primary-600 dark:text-primary-400"
                        >
                            {{ stats.users }}
                        </div>
                        <div class="text-gray-600 dark:text-gray-400">
                            Developers
                        </div>
                    </div>
                    <div>
                        <div
                            class="text-3xl font-bold text-primary-600 dark:text-primary-400"
                        >
                            {{ stats.posts }}
                        </div>
                        <div class="text-gray-600 dark:text-gray-400">
                            Posts
                        </div>
                    </div>
                    <div>
                        <div
                            class="text-3xl font-bold text-primary-600 dark:text-primary-400"
                        >
                            {{ stats.jobs }}
                        </div>
                        <div class="text-gray-600 dark:text-gray-400">Jobs</div>
                    </div>
                    <div>
                        <div
                            class="text-3xl font-bold text-primary-600 dark:text-primary-400"
                        >
                            {{ stats.events }}
                        </div>
                        <div class="text-gray-600 dark:text-gray-400">
                            Events
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Main Content -->
        <section class="py-12">
            <div class="container mx-auto px-4">
                <div class="grid lg:grid-cols-3 gap-8">
                    <!-- Posts Feed -->
                    <div class="lg:col-span-2">
                        <div class="flex items-center justify-between mb-6">
                            <h2
                                class="text-2xl font-bold text-gray-900 dark:text-white"
                            >
                                Recent Posts
                            </h2>
                            <router-link
                                to="/community"
                                class="text-primary-600 dark:text-primary-400 hover:underline"
                            >
                                View All
                            </router-link>
                        </div>

                        <div v-if="loading" class="space-y-4">
                            <div
                                v-for="i in 3"
                                :key="i"
                                class="bg-white dark:bg-gray-800 rounded-lg p-6 animate-pulse"
                            >
                                <div
                                    class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-3/4 mb-4"
                                ></div>
                                <div
                                    class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-1/2 mb-4"
                                ></div>
                                <div
                                    class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-full"
                                ></div>
                            </div>
                        </div>

                        <div v-else class="space-y-4">
                            <PostCard
                                v-for="post in posts"
                                :key="post.id"
                                :post="post"
                            />
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="space-y-8">
                        <!-- Categories -->
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-6">
                            <h3
                                class="text-lg font-semibold text-gray-900 dark:text-white mb-4"
                            >
                                Categories
                            </h3>
                            <div class="space-y-2">
                                <router-link
                                    v-for="category in categories"
                                    :key="category.id"
                                    :to="`/community/${category.slug}`"
                                    class="flex items-center justify-between p-2 rounded hover:bg-gray-100 dark:hover:bg-gray-700 transition"
                                >
                                    <span
                                        class="text-gray-700 dark:text-gray-300"
                                        >{{ category.name }}</span
                                    >
                                    <span
                                        class="text-sm text-gray-500 dark:text-gray-400"
                                        >{{ category.posts_count }}</span
                                    >
                                </router-link>
                            </div>
                        </div>

                        <!-- Upcoming Events -->
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-6">
                            <h3
                                class="text-lg font-semibold text-gray-900 dark:text-white mb-4"
                            >
                                Upcoming Events
                            </h3>
                            <div
                                v-if="events.length === 0"
                                class="text-gray-500 dark:text-gray-400"
                            >
                                No upcoming events
                            </div>
                            <div v-else class="space-y-4">
                                <EventCard
                                    v-for="event in events"
                                    :key="event.id"
                                    :event="event"
                                />
                            </div>
                            <router-link
                                to="/events"
                                class="block mt-4 text-center text-primary-600 dark:text-primary-400 hover:underline"
                            >
                                View All Events
                            </router-link>
                        </div>

                        <!-- Top Users -->
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-6">
                            <h3
                                class="text-lg font-semibold text-gray-900 dark:text-white mb-4"
                            >
                                Top Contributors
                            </h3>
                            <div class="space-y-3">
                                <router-link
                                    v-for="(user, index) in topUsers"
                                    :key="user.id"
                                    :to="`/profile/${user.username}`"
                                    class="flex items-center gap-3 p-2 rounded hover:bg-gray-100 dark:hover:bg-gray-700 transition"
                                >
                                    <span
                                        class="text-sm font-medium text-gray-500 dark:text-gray-400 w-6"
                                        >#{{ index + 1 }}</span
                                    >
                                    <img
                                        :src="user.avatar"
                                        :alt="user.name"
                                        class="w-8 h-8 rounded-full"
                                    />
                                    <div class="flex-1 min-w-0">
                                        <div
                                            class="text-sm font-medium text-gray-900 dark:text-white truncate"
                                        >
                                            {{ user.name }}
                                        </div>
                                        <div
                                            class="text-xs text-gray-500 dark:text-gray-400"
                                        >
                                            {{ user.reputation }} rep
                                        </div>
                                    </div>
                                </router-link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useAuthStore } from "@/stores/auth";
import axios from "axios";
import PostCard from "@/components/PostCard.vue";
import EventCard from "@/components/EventCard.vue";

const authStore = useAuthStore();

const loading = ref(true);
const stats = ref({ users: 0, posts: 0, jobs: 0, events: 0 });
const posts = ref([]);
const categories = ref([]);
const events = ref([]);
const topUsers = ref([]);

onMounted(async () => {
    try {
        const [statsRes, postsRes, categoriesRes, eventsRes, usersRes] =
            await Promise.all([
                axios.get("/v1/stats"),
                axios.get("/v1/posts?per_page=5"),
                axios.get("/v1/categories"),
                axios.get("/v1/events?upcoming=true&per_page=3"),
                axios.get("/v1/users/leaderboard?per_page=5"),
            ]);

        stats.value = statsRes.data.data;
        posts.value = postsRes.data.data;
        categories.value = categoriesRes.data.data;
        events.value = eventsRes.data.data;
        topUsers.value = usersRes.data.data;
    } catch (error) {
        console.error("Failed to load home data:", error);
    } finally {
        loading.value = false;
    }
    // console.log(posts);
});
</script>
