<template>
  <div></div>
</template>

<script lang="ts">
// fetch.js
import { ref, watchEffect, toValue } from "vue";

export function useFetch({ url }: { url: any }) {
  const data = ref(null);
  const error = ref(null);

  const fetchData = () => {
    // reset state before fetching..
    data.value = null;
    error.value = null;

    fetch(toValue(url))
      .then((res) => res.json())
      .then((json) => (data.value = json))
      .catch((err) => (error.value = err));
  };

  watchEffect(() => {
    fetchData();
  });

  return { data, error };
}
</script>

<style></style>
