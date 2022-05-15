cenozoApp.defineModule({
  name: "code_type",
  models: ["add", "list", "view"],
  create: (module) => {
    angular.extend(module, {
      identifier: {},
      name: {
        singular: "code type",
        plural: "code types",
        possessive: "code type's",
      },
      columnList: {
        code: {
          title: "Code",
        },
        apex_deployment_count: {
          title: "Apex Deployments",
          type: "number",
        },
        scan_type_count: {
          title: "Scan Types",
          type: "number",
        },
        description: {
          title: "Description",
        },
      },
      defaultOrder: {
        column: "code_type.code",
        reverse: false,
      },
    });

    module.addInputGroup("", {
      code: {
        title: "Code",
        type: "string",
      },
      description: {
        title: "Description",
        type: "text",
      },
    });
  },
});
