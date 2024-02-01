<template>
  <div>
    <ul>
      <li v-for="repo in repositories" :key="repo.id">
        <p>{{ repo.name }}</p>
        <a :href="repo.html_url" target="_blank">{{ repo.html_url }}</a>
      </li>
    </ul>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref, onMounted } from "vue";

export default defineComponent({
  name: "DashboardView",

  setup() {
    const repositories = ref([]);

    const fetchRepositories = async () => {
      try {
        const response = await fetch("http://localhost:8000/api/repositories", {
          method: "GET",
          credentials: "include",
        });
        if (!response.ok) {
          throw new Error(`Erreur HTTP: ${response.status}`);
        }
        const data = await response.json();
        repositories.value = data.repo; // Assurez-vous que la structure des données correspond
      } catch (error) {
        console.error("Erreur lors de la récupération des données:", error);
      }
    };

    onMounted(fetchRepositories);

    return { repositories };
  },
});
</script>

<style></style>
