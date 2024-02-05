<template>
  <div>
    <!-- Afficher le gif d'attente si isLoading est vrai -->
    <div v-if="isLoading">
      <img src="../assets/XOsX.gif" alt="Chargement..." />
    </div>
    <ul class="ul" v-else>
      <li class="liste" v-for="repo in repositories" :key="repo.id">
        <h2 class="titre">{{ repo.name }}</h2>
        <a class="lien" :href="repo.html_url" target="_blank">{{
          repo.html_url
        }}</a>
        <button v-on:click="runAnalysis(repo.name)">Analyser</button>
      </li>
    </ul>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref, onMounted } from "vue";
export default defineComponent({
  name: "DashboardView",

  setup() {
    const isLoading = ref(true);
    const repositories = ref([]);

    const fetchRepositories = async () => {
      isLoading.value = true; // Commencer le chargement
      try {
        const response = await fetch("http://localhost:8000/api/repositories", {
          headers: { "Content-Type": "application/json" },
          method: "GET",
          credentials: "include",
        });
        if (!response.ok) {
          throw new Error(`Erreur HTTP: ${response.status}`);
        }
        const data = await response.json();
        repositories.value = data.repo; // Mise à jour des données
      } catch (error) {
        console.error("Erreur lors de la récupération des données:", error);
      } finally {
        isLoading.value = false; // Fin du chargement
      }
    };

    const runAnalysis = async (repoName: string) => {
      try {
        const response = await fetch(
          `http://localhost:8000/api/analysis/${repoName}`,
          {
            headers: { "Content-Type": "application/json" },
            method: "POST",
            credentials: "include",
            body: JSON.stringify({ analysis: "php_stan" }),
          }
        );
        if (!response.ok) {
          throw new Error(`Erreur HTTP: ${response.status}`);
        }
        const data = await response.json();
        console.log(data);
      } catch (error) {
        console.error("Erreur lors de la récupération des données:", error);
      }
    };

    onMounted(fetchRepositories);

    return { repositories, isLoading, runAnalysis };
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
