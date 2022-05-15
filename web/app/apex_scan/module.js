cenozoApp.defineModule({
  name: "apex_scan",
  models: ["list", "view"],
  create: (module) => {
    angular.extend(module, {
      identifier: {
        parent: {
          subject: "apex_exam",
          column: "apex_exam.id",
        },
      },
      name: {
        singular: "apex scan",
        plural: "apex scans",
        possessive: "apex scan's",
      },
      columnList: {
        uid: {
          column: "participant.uid",
          title: "Participant",
        },
        rank: {
          column: "apex_exam.rank",
          title: "Rank",
        },
        scan_type_side: {
          title: "Scan Type",
        },
        availability: {
          title: "Available",
          type: "boolean",
        },
        invalid: {
          title: "Invalid",
          type: "boolean",
        },
        priority: {
          column: "priority",
          title: "Priority",
          type: "boolean",
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
      rank: {
        column: "apex_exam.rank",
        title: "Wave Rank",
        type: "string",
        isConstant: true,
      },
      scan_type_type: {
        column: "scan_type.type",
        title: "Type",
        type: "string",
        isConstant: true,
      },
      scan_type_side: {
        column: "scan_type.side",
        title: "Side",
        type: "string",
        isConstant: true,
      },
      availability: {
        title: "Availability",
        type: "boolean",
        isConstant: true,
      },
      invalid: {
        title: "Invalid",
        type: "boolean",
        isConstant: true,
      },
      priority: {
        title: "Priority",
        type: "boolean",
      },
      scan_datetime: {
        title: "Scan Date & Time",
        type: "datetime",
        isConstant: true,
      },
      scanid: {
        title: "Scan ID",
        type: "string",
        isConstant: true,
      },
      patient_key: {
        title: "Patient Key",
        type: "string",
        isConstant: true,
      },
      forearm_length: {
        title: "Forearm Length",
        type: "string",
        format: "float",
        isConstant: true,
        isExcluded: function ($state, model) {
          return (
            angular.isUndefined(model.viewModel.record.scan_type_type) ||
            "forearm" != model.viewModel.record.scan_type_type
          );
        },
      },
    });
  },
});
