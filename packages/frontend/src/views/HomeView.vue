<template>
  <div class="auth-view">
    <img src="../assets/codeflow.png" alt="" />
    <hello-world>Authentification</hello-world>
  </div>
</template>

<script lang="ts">
import { defineComponent } from "vue";
import HelloWorld from "@/components/auth.vue"; // @ is an alias to /src

export default defineComponent({
  name: "AuthView",
  components: {
    HelloWorld,
  },
});
</script>
<style>
img {
  margin: auto;
  width: 20%;
  height: 20%;
}
</style>
