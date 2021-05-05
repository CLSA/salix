define( function() {
  'use strict';

  try { var module = cenozoApp.module( 'apex_deployment', true ); } catch( err ) { console.warn( err ); return; }
  angular.extend( module, {
    identifier: {
      parent: {
        subject: 'apex_host',
        column: 'apex_host.name'
      }
    },
    name: {
      singular: 'apex deployment',
      plural: 'apex deployments',
      possessive: 'apex deployment\'s'
    },
    columnList: {
      uid: {
        column: 'participant.uid',
        title: 'Participant'
      },
      rank: {
        column: 'apex_exam.rank',
        title: 'Wave Rank',
        type: 'rank'
      },
      first_barcode: {
        column: 'first_apex_exam.barcode',
        title: 'First Barcode'
      },
      barcode: {
        column: 'apex_exam.barcode',
        title: 'Barcode'
      },
      scan_type_side: {
        title: 'Scan Type'
      },
      apex_host: {
        column: 'apex_host.name',
        title: 'Apex Host'
      },
      merged: {
        title: 'Merged',
        type: 'boolean'
      },
      status: {
        title: 'Status'
      },
      priority: {
        column: 'apex_scan.priority',
        title: 'Priority',
        type: 'boolean'
      },
      pass: {
        title: 'Pass',
        type: 'boolean'
      },
      code_summary: {
        column: 'apex_deployment_code_summary.summary',
        title: 'Code Summary'
      }
    },
    defaultOrder: {
      column: 'apex_host.id',
      reverse: false
    }
  } );

  module.addInputGroup( '', {
    apex_host_id: {
      title: 'Apex Host',
      type: 'enum',
      isConstant: true
    },
    uid: {
      column: 'participant.uid',
      title: 'Participant',
      type: 'string',
      isConstant: true
    },
    barcode: {
      column: 'apex_exam.barcode',
      title: 'Barcode',
      type: 'string',
      isConstant: true
    },
    rank: {
      column: 'apex_exam.rank',
      title: 'Wave Rank',
      type: 'string',
      isConstant: true
    },
    scan_type_side: {
      column: 'scan_type_side',
      title: 'Scan Type & Side',
      type: 'string',
      isConstant: true
    },
    pass: { type: 'boolean', isExcluded: true }, // used by CnApexDeploymentViewFactory::patch
    note: { type: 'hidden' },
    scan_type_id: { column: 'apex_scan.scan_type_id', type: 'hidden' }, // used to restrict code types

    // the following are used for the next analysis button
    scan_type_type: { column: 'scan_type.type', type: 'hidden' },
    priority: { column: 'apex_scan.priority', type: 'hidden' },
    first_barcode: { column: 'first_apex_exam.barcode', type: 'hidden' }
  } );

  module.addInputGroup( 'Additional Details', {
    merged: {
      title: 'Merged',
      type: 'boolean',
      isExcluded: 'add',
      isConstant: true
    },
    status: {
      title: 'Status',
      type: 'string',
      isExcluded: 'add',
      isConstant: true
    },
    comp_scanid: {
      title: 'Scan ID',
      isExcluded: 'add',
      type: 'string',
      isConstant: true
    },
    site: {
      column: 'site.name',
      title: 'Site',
      type: 'string',
      isExcluded: 'add',
      isConstant: true
    },
    serial_number_id: {
      column: 'serial_number.id',
      title: 'Serial Number',
      type: 'string',
      isExcluded: 'add',
      isConstant: true
    },
    forearm_length: {
      column: 'apex_scan.forearm_length',
      title: 'Forearm Length',
      type: 'string',
      format: 'float',
      isConstant: true,
      isExcluded: function( $state, model ) {
        // only show the forearm length when viewing forearm scans
        return angular.isUndefined( model.viewModel.record.scan_type_type ) || 'forearm' != model.viewModel.record.scan_type_type;
      }
    },
    analysis_datetime: {
      title: 'Analysis Date & Time',
      type: 'datetime',
      isExcluded: 'add',
      isConstant: true
    },
    export_datetime: {
      title: 'Export Date & Time',
      type: 'datetime',
      isExcluded: 'add',
      isConstant: true
    },
    import_datetime: {
      title: 'Import Date & Time',
      type: 'datetime',
      isExcluded: 'add',
      isConstant: true
    },
  } );

  module.addExtraOperation( 'view', {
    title: 'Download Report',
    isIncluded: function( $state, model ) { return model.canDownloadReports(); },
    isDisabled: function( $state, model ) { return !model.viewModel.fileExists; },
    operation: async function( $state, model ) { await model.viewModel.downloadReport(); }
  } );

  /* ######################################################################################################## */
  cenozo.providers.directive( 'cnApexDeploymentAdd', [
    'CnApexDeploymentModelFactory',
    function( CnApexDeploymentModelFactory ) {
      return {
        templateUrl: module.getFileUrl( 'add.tpl.html' ),
        restrict: 'E',
        scope: { model: '=?' },
        controller: function( $scope ) {
          if( angular.isUndefined( $scope.model ) ) $scope.model = CnApexDeploymentModelFactory.root;
        }
      };
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.directive( 'cnApexDeploymentList', [
    'CnApexDeploymentModelFactory',
    function( CnApexDeploymentModelFactory ) {
      return {
        templateUrl: module.getFileUrl( 'list.tpl.html' ),
        restrict: 'E',
        scope: { model: '=?' },
        controller: function( $scope ) {
          if( angular.isUndefined( $scope.model ) ) $scope.model = CnApexDeploymentModelFactory.root;
        }
      };
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.directive( 'cnApexDeploymentView', [
    'CnApexDeploymentModelFactory',
    function( CnApexDeploymentModelFactory ) {
      return {
        templateUrl: module.getFileUrl( 'view.tpl.html' ),
        restrict: 'E',
        scope: { model: '=?' },
        controller: function( $scope ) {
          if( angular.isUndefined( $scope.model ) ) $scope.model = CnApexDeploymentModelFactory.root;
          $scope.booleanEnumList = [
            { value: '', name: '(empty)' },
            { value: true, name: 'Yes' },
            { value: false, name: 'No' }
          ];
        }
      };
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.factory( 'CnApexDeploymentAddFactory', [
    'CnBaseAddFactory',
    function( CnBaseAddFactory ) {
      var object = function( parentModel ) { CnBaseAddFactory.construct( this, parentModel ); };
      return { instance: function( parentModel ) { return new object( parentModel ); } };
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.factory( 'CnApexDeploymentListFactory', [
    'CnBaseListFactory',
    function( CnBaseListFactory ) {
      var object = function( parentModel ) { CnBaseListFactory.construct( this, parentModel ); };
      return { instance: function( parentModel ) { return new object( parentModel ); } };
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.factory( 'CnApexDeploymentViewFactory', [
    'CnBaseViewFactory', 'CnHttpFactory', 'CnSession', 'CnModalMessageFactory', 'CnModalConfirmFactory', '$state',
    function( CnBaseViewFactory, CnHttpFactory, CnSession, CnModalMessageFactory, CnModalConfirmFactory, $state ) {
      var object = function( parentModel, root ) {
        CnBaseViewFactory.construct( this, parentModel, root, 'analysis' );
        angular.extend( this, {
          isComplete: false,
          fileExists: false,

          // create a custom child list that includes the analysis dialog
          customChildList: null,
          getChildList: function() {
            if( null == this.customChildList ) {
              this.customChildList = this.$$getChildList().concat( [
                { subject: { camel: 'analysis', snake: 'analysis' } }
              ] );
            }
            return this.customChildList;
          },

          // extend the child title to properly label the analysis dialog
          getChildTitle: function( child ) {
            return 'analysis' == child.subject.snake ? 'Analysis' : this.$$getChildTitle( child );
          },

          downloadReport: async function() {
            await CnHttpFactory.instance( {
              path: 'apex_deployment/' + this.record.getIdentifier(),
              format: 'jpeg'
            } ).file();
          },

          // transitions to the next available deployment for analysis
          transitionOnNextViewState: async function( any ) {
            if( angular.isUndefined( any ) ) any = false;

            var where = [ { column: 'status', operator: '=', value: 'pending' } ];
            if( !any ) where.push( { column: 'scan_type.type', operator: '=', value: this.record.scan_type_type } );

            var response = await CnHttpFactory.instance( {
              path: 'apex_host/' + this.record.apex_host_id + '/apex_deployment',
              // get the highest priority record
              data: {
                select: { column: [ 'id', { table: 'first_apex_exam', column: 'barcode' } ] },
                modifier: {
                  // make sure it comes after the current deployment
                  where: where.concat( [
                    { column: 'apex_scan.priority', operator: '<=', value: this.record.priority ? 1 : 0 },
                    { column: 'apex_exam.rank', operator: '>=', value: this.record.rank },
                    { column: 'first_apex_exam.barcode', operator: '>=', value: this.record.first_barcode },
                    { column: 'apex_exam.barcode', operator: '>', value: this.record.barcode }
                  ] ),
                  order: [
                    { 'apex_scan.priority': true },
                    { 'apex_exam.rank': false },
                    { 'first_apex_exam.barcode': false },
                    { 'apex_exam.barcode': false }
                  ],
                  limit: 1
                }
              }
            } ).get();

            if( 0 == response.data.length ) {
              // restart at the beginning if we didn't get any records back
              var response = await CnHttpFactory.instance( {
                path: 'apex_host/' + this.record.apex_host_id + '/apex_deployment',
                data: {
                  select: { column: [ 'id', { table: 'first_apex_exam', column: 'barcode' } ] },
                  modifier: {
                    // don't add the extra where statements from above to start from the beginning of the list
                    where: where,
                    order: [
                      { 'apex_scan.priority': true },
                      { 'apex_exam.rank': false },
                      { 'first_apex_exam.barcode': false },
                      { 'apex_exam.barcode': false }
                    ],
                    limit: 1
                  }
                }
              } ).get();

              if( 0 < response.data.length ) await $state.go( 'apex_deployment.view', { identifier: response.data[0].id } );
            } else {
              await $state.go( 'apex_deployment.view', { identifier: response.data[0].id } );
            }
          },

          onView: async function( force ) {
            var self = this;
            this.isComplete = false;
            this.fileExists = false;
            await this.$$onView( force );

            // do not allow exported deployments to be edited
            this.parentModel.getEditEnabled = null == this.record.status || 'exported' == this.record.status
                                            ? function() { return false; }
                                            : function() { return self.parentModel.$$getEditEnabled(); };

            await this.parentModel.metadata.getPromise();

            // get a limited list of code types which apply to this deployment's scan type
            this.codeTypeList = this.parentModel.metadata.codeTypeList.filter(
              codeType => codeType.scan_type_id_list.includes( this.record.scan_type_id )
            );

            this.isComplete = true;
            this.codeTypeList.forEach( function( codeType ) { self.record['codeType'+codeType.id] = false; } );

            // set all code values to false then get the scan's codes
            var response = await CnHttpFactory.instance( {
              path: 'apex_deployment/' + this.record.getIdentifier() + '/code',
              data: { select: { column: [ 'code_type_id' ] } }
            } ).query();

            response.data.forEach( function( code ) { self.record['codeType'+code.code_type_id] = true; } );

            // determine whether the report is available
            var response = await CnHttpFactory.instance( {
              path: 'apex_deployment/' + this.record.getIdentifier() + '?report=1'
            } ).get();

            this.fileExists = response.data;
          },

          patch: async function( property ) {
            var self = this;
            if( property.match( /^codeType/ ) ) {
              var newValue = this.record[property];
              var codeTypeId = property.replace( /^codeType/, '' );
              if( newValue ) {
                await CnHttpFactory.instance( {
                  path: 'code',
                  data: {
                    apex_deployment_id: this.record.getIdentifier(),
                    code_type_id: codeTypeId,
                    user_id: CnSession.user.id
                  },
                  onError: function( error ) {
                    // ignore 409 (code already exists)
                    if( 409 != error.status ) {
                      self.record[property] = !self.record[property];
                      CnModalMessageFactory.httpError( error );
                    }
                  }
                } ).post()
              } else {
                await CnHttpFactory.instance( {
                  path: 'code/apex_deployment_id=' + this.record.getIdentifier() + ';code_type_id=' + codeTypeId,
                  onError: function( error ) {
                    // ignore 404 (code has already been deleted)
                    if( 404 != error.status ) {
                      self.record[property] = !self.record[property];
                      CnModalMessageFactory.httpError( error );
                    }
                  }
                } ).delete();
              }
            } else {
              var codeList = [];
              var resetCodes = false;

              // check if pass is being set to null (which at this point will be an empty string)
              if( 'pass' == property && '' === this.record.pass ) {
                // gather all selected code types
                for( var column in this.record )
                  if( column.match( /^codeType/ ) && this.record[column] )
                    codeList.push( column );

                // if there are any code types, offer to reset them
                if( 0 < codeList.length ) {
                  resetCodes = await CnModalConfirmFactory.instance( {
                    title: 'Reset Codes',
                    message: 'Do you also wish to remove all codes associated with this deployment?'
                  } ).show();
                }
              }

              var data = {};
              data[property] = this.record[property];
              if( resetCodes ) data.reset_codes = true;
              await this.onPatch( data );
              if( 'pass' == property ) await this.onView();
            }
          }
        } );

        var self = this;
        async function init() {
          // customize the scan list heading
          await this.deferred.promise;
          if( angular.isDefined( self.apexDeploymentModel ) )
            self.apexDeploymentModel.listModel.heading = 'Sibling Apex Deployment List';
        }

        init();
      };
      return { instance: function( parentModel, root ) { return new object( parentModel, root ); } };
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.factory( 'CnApexDeploymentModelFactory', [
    'CnBaseModelFactory',
    'CnApexDeploymentAddFactory', 'CnApexDeploymentListFactory', 'CnApexDeploymentViewFactory',
    'CnSession', 'CnHttpFactory',
    function( CnBaseModelFactory,
              CnApexDeploymentAddFactory, CnApexDeploymentListFactory, CnApexDeploymentViewFactory,
              CnSession, CnHttpFactory ) {
      var object = function( root ) {
        CnBaseModelFactory.construct( this, module );
        this.addModel = CnApexDeploymentAddFactory.instance( this );
        this.listModel = CnApexDeploymentListFactory.instance( this );
        this.viewModel = CnApexDeploymentViewFactory.instance( this, root );

        this.canDownloadReports = function() { return 3 <= CnSession.role.tier; };

        // extend getServiceData
        this.getServiceData = function( type, columnRestrictLists ) {
          var data = this.$$getServiceData( type, columnRestrictLists );
          if( 'apex_deployment' == this.getSubjectFromState() && 'view' == this.getActionFromState() )
            data.sibling_apex_deployment_id = this.getQueryParameter( 'identifier' );
          return data;
        };

        // extend getMetadata
        this.getMetadata = async function() {
          var self = this;
          await this.$$getMetadata();

          var response = await CnHttpFactory.instance( {
            path: 'apex_host',
            data: {
              select: { column: [ 'id', 'name' ] },
              modifier: { order: { name: false }, limit: 1000 }
            }
          } ).query();

          this.metadata.columnList.apex_host_id.enumList = [];
          response.data.forEach( function( item ) {
            self.metadata.columnList.apex_host_id.enumList.push( {
              value: item.id,
              name: item.name
            } );
          } );

          var response = await CnHttpFactory.instance( {
            path: 'code_type',
            data: {
              select: { column: [ 'id', 'code', 'description', 'scan_type_id_list' ] },
              modifier: { order: 'code', limit: 1000 }
            }
          } ).query();

          // convert the id list into an array if integers
          response.data.forEach(
            item => item.scan_type_id_list = item.scan_type_id_list.split( ',' ).map( id => parseInt(id) )
          );
          this.metadata.codeTypeList = response.data;
        };
      };

      return {
        root: new object( true ),
        instance: function() { return new object( false ); }
      };
    }
  ] );

} );
