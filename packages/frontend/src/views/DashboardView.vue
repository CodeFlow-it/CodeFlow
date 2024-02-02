<template>
  <div>
    <ul class="ul">
      <li class="liste" v-for="repo in repositories" :key="repo.id">
        <h2 class="titre">{{ repo.name }}</h2>
        <a class="lien" :href="repo.html_url" target="_blank">{{
          repo.html_url
        }}</a>
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

<style>
.titre {
  font-weight: bold;
  font-size: larger;
}

.ul {
  padding-left: 20px;
  list-style-type: none;
  margin: 0;
}

.liste {
  padding: 8px 0;
  border-bottom: 1px solid #ccc;
  margin-bottom: 5px;
}

.liste::before {
  color: #007bff;
  font-weight: bold;
  display: inline-block;
  width: 1em;
  margin-left: -1em;
}

.liste:first-child {
  padding-top: 15px;
}

.liste:last-child {
  border-bottom: none;
  padding-bottom: 15px;
  margin-bottom: 0;
}

.lien {
  color: #cccccc;
  text-decoration: underline;
}
</style>
