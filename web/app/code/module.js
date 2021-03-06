cenozoApp.defineModule({
  name: "code",
  models: ["add", "list", "view"],
  create: (module) => {
    angular.extend(module, {
      identifier: {},
      name: {
        singular: "code",
        plural: "codes",
        possessive: "code's",
      },
      columnList: {
        code_type: {
          column: "code_type.code",
          title: "Code Type",
        },
        user: {
          column: "user.name",
          title: "User",
        },
      },
      defaultOrder: {
        column: "code_type.code",
        reverse: false,
      },
    });

    module.addInputGroup("", {
      code_type_id: {
        title: "Code Type",
        type: "enum",
      },
      user_id: {
        title: "User",
        type: "lookup-typeahead",
        typeahead: {
          table: "user",
          select:
            'CONCAT( user.first_name, " ", user.last_name, " (", user.name, ")" )',
          where: ["user.first_name", "user.last_name", "user.name"],
        },
      },
    });

    /* ############################################################################################## */
    cenozo.providers.factory("CnCodeModelFactory", [
      "CnBaseModelFactory",
      "CnCodeAddFactory",
      "CnCodeListFactory",
      "CnCodeViewFactory",
      "CnHttpFactory",
      function (
        CnBaseModelFactory,
        CnCodeAddFactory,
        CnCodeListFactory,
        CnCodeViewFactory,
        CnHttpFactory
      ) {
        var object = function (root) {
          CnBaseModelFactory.construct(this, module);
          this.addModel = CnCodeAddFactory.instance(this);
          this.listModel = CnCodeListFactory.instance(this);
          this.viewModel = CnCodeViewFactory.instance(this, root);

          // extend getMetadata
          this.getMetadata = async function () {
            var self = this;
            await this.$$getMetadata();

            var response = await CnHttpFactory.instance({
              path: "code_type",
              data: {
                select: { column: ["id", "code"] },
                modifier: { order: "code", limit: 1000 },
              },
            }).query();

            this.metadata.columnList.code_type_id.enumList = [];
            response.data.forEach(function (item) {
              self.metadata.columnList.code_type_id.enumList.push({
                value: item.id,
                name: item.code,
              });
            });
          };
        };

        return {
          root: new object(true),
          instance: function () {
            return new object(false);
          },
        };
      },
    ]);
  },
});
