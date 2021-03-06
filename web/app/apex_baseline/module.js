cenozoApp.defineModule({
  name: "apex_baseline",
  models: ["list", "view"],
  create: (module) => {
    angular.extend(module, {
      identifier: "participant.uid",
      name: {
        singular: "apex baseline",
        plural: "apex baselines",
        possessive: "apex baseline's",
      },
      columnList: {
        uid: {
          column: "participant.uid",
          title: "Participant",
        },
        dob: {
          title: "Date of Birth",
          type: "dob",
        },
        ethnicity: {
          title: "Ethnicity",
        },
        sex: {
          title: "Sex",
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
      },
      dob: {
        title: "Date of Birth",
        type: "dob",
      },
      ethnicity: {
        title: "Ethnicity",
        type: "string",
      },
      sex: {
        title: "Sex",
        type: "string",
      },
    });
  },
});
