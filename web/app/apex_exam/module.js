cenozoApp.defineModule({
  name: "apex_exam",
  models: ["list", "view"],
  create: (module) => {
    angular.extend(module, {
      identifier: {
        parent: {
          subject: "apex_baseline",
          column: "participant.uid",
        },
      },
      name: {
        singular: "apex exam",
        plural: "apex exams",
        possessive: "apex exam's",
        friendlyColumn: "rank",
      },
      columnList: {
        uid: {
          column: "participant.uid",
          title: "Participant",
        },
        site: {
          column: "site.name",
          title: "Site",
        },
        serial_number_id: {
          title: "Serial Number",
        },
        barcode: {
          title: "Barcode",
        },
        rank: {
          title: "Wave Rank",
          type: "rank",
        },
        technician: {
          title: "Technician",
          type: "string",
        },
      },
      defaultOrder: {
        column: "participant.uid",
        reverse: false,
      },
    });

    module.addInputGroup("", {
      participant: {
        column: "participant.uid",
        title: "Participant",
        type: "string",
        isConstant: true,
      },
      site: {
        column: "site.name",
        title: "Site",
        type: "string",
      },
      serial_number_id: {
        title: "Serial Number",
        type: "string",
      },
      barcode: {
        title: "Barcode",
        type: "string",
      },
      rank: {
        title: "Wave Rank",
        type: "rank",
      },
      technician: {
        title: "Technician",
        type: "string",
      },
      height: {
        title: "Height",
        type: "string",
      },
      weight: {
        title: "Weight",
        type: "string",
      },
      age: {
        title: "Age",
        type: "string",
      },
    });
  },
});
