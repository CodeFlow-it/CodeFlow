<template>
  <div class="auth-view">
    <img src="../assets/codeflow.png" alt="" />
    <hello-world>Authentification</hello-world>
    <ButtonComp msg="Connexion" onclick="setup()"></ButtonComp>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref, onMounted } from "vue";
import { useRouter } from "vue-router";
import HelloWorld from "@/components/auth.vue"; // @ is an alias to /src
import ButtonComp from "@/components/button.vue";

export default defineComponent({
  name: "AuthView",
  setup() {
    const router = useRouter();
    const apiUrl = ref(process.env.VUE_APP_GITHUB_SSO);

    onMounted(async () => {
      try {
        const response = await fetch(apiUrl.value);

        // Vérifiez si la réponse est réussie
        if (!response.ok) {
          throw new Error(`Erreur HTTP: ${response.status}`);
        }

        // Tentez de lire la réponse en tant que JSON
        const data = await response.json();

        if (data.message === "Authentication success") {
          router.push("/path-to-your-repositories-page");
        }
      } catch (error) {
        console.error("Erreur lors de la récupération de l'API:", error);
        // Vous pourriez aussi examiner ici le contenu brut de 'error'
      }
    });

    return { apiUrl };
  },

  components: {
    HelloWorld,
    ButtonComp,
  },
});
</script>
<style>
img {
  margin: auto;
  width: 20%;
  height: 20%;
}

hello-world {
  color: white;
  font-size: larger;
}
</style>
