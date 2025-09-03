import callApi from "src/assets/call-api";
import { useStore } from "../store";

async function refreshData(storeVar, path) {
  const response = await callApi({ path, method: "get" });
  storeVar = response.data;
  return;
}

export default async (storeVar, path) => {
  const store = useStore();

  // Return cached data immediately
  if (storeVar !== null) {
    // But also refresh in background
    refreshData(storeVar, path).catch(() => {
      // Silent fail - we already have data
    });
    return storeVar;
  } else {
    storeVar = await callApi({ path, method: "get" });
  }
};
