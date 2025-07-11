import { clone } from "lodash-es";
import { useStore } from "../store";
import { Loading, Notify } from "quasar";
import callApi from "src/assets/call-api";

const send = async (payload) => {
  Loading.show();

  const response = await callApi({
    path: "/contact",
    method: "post",
    payload,
  });

  if (response.status == 202) {
    Notify.create({
      type: "positive",
      message: "Contact message sent",
    });

    setTimeout(() => {
      window.location.reload();
    }, 3000);
  }
};

export default async (contact) => {
  const store = useStore();

  contact = clone(contact);

  if (!!contact.message) {
    await send(contact);
    return;
  }

  Notify.create({
    type: "warning",
    message: "Do you want to send with an empty Message field?",
    actions: [
      {
        label: "No",
      },
      {
        label: "Yes",
        handler: async () => {
          await send(contact);
        },
      },
    ],
  });
};
