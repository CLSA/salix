cenozoApp.defineModule({
  name: "apex_host",
  models: ["list", "view"],
  defaultTab: "apex_deployment",
  create: (module) => {
    angular.extend(module, {
      identifier: {},
      name: {
        singular: "apex host",
        plural: "apex hosts",
        possessive: "apex host's",
      },
      columnList: {
        name: {
          title: "Name",
        },
        host: {
          title: "Hostname",
        },
        allocations: {
          title: "Allocations",
        },
        apex_scan_count: {
          title: "Apex Scans",
          type: "number",
        },
        participant_count: {
          title: "Participants",
          type: "number",
        },
      },
      defaultOrder: {
        column: "apex_host.name",
        reverse: false,
      },
    });

    module.addInputGroup("", {
      name: {
        title: "Name",
        type: "string",
      },
      host: {
        title: "Hostname",
        type: "string",
      },
      apex_scan_count: {
        title: "Apex Scans",
        type: "string",
        isConstant: true,
      },
      participant_count: {
        title: "Participants",
        type: "string",
        isConstant: true,
      },
    });
  },
});
