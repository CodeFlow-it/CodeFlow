<template>
  <div class="auth-view">
    <img src="../assets/codeflow.png" alt="" />
    <hello-world>Authentification</hello-world>
    <ButtonComp msg="Connexion" :url="apiUrl"></ButtonComp>
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
    const apiUrl = ref("http://localhost:8000/connect/github");

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
