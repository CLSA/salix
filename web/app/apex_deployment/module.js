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
        title: 'pass',
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
      constant: true
    },
    uid: {
      column: 'participant.uid',
      title: 'Participant',
      type: 'string',
      constant: true
    },
    barcode: {
      column: 'apex_exam.barcode',
      title: 'Barcode',
      type: 'string',
      constant: true
    },
    rank: {
      column: 'apex_exam.rank',
      title: 'Wave Rank',
      type: 'string',
      constant: true
    },
    scan_type_side: {
      column: 'scan_type_side',
      title: 'Scan Type & Side',
      type: 'string',
      constant: true
    },
    pass: { type: 'boolean', exclude: true }, // used by CnApexDeploymentViewFactory::patch
    note: { type: 'hidden' },
    scan_type_id: { column: 'apex_scan.scan_type_id', type: 'hidden' }, // used to restrict code types
    scan_type_type: { column: 'scan_type.type', type: 'hidden' } // used for next analysis buttons
  } );

  module.addInputGroup( 'Additional Details', {
    merged: {
      title: 'Merged',
      type: 'boolean',
      exclude: 'add',
      constant: true
    },
    status: {
      title: 'Status',
      type: 'string',
      exclude: 'add',
      constant: true
    },
    comp_scanid: {
      title: 'Scan ID',
      exclude: 'add',
      type: 'string',
      constant: true
    },
    site: {
      column: 'site.name',
      title: 'Site',
      type: 'string',
      exclude: 'add',
      constant: true
    },
    serial_number_id: {
      column: 'serial_number.id',
      title: 'Serial Number',
      type: 'string',
      exclude: 'add',
      constant: true
    },
    analysis_datetime: {
      title: 'Analysis Date & Time',
      type: 'datetime',
      exclude: 'add',
      constant: true
    },
    export_datetime: {
      title: 'Export Date & Time',
      type: 'datetime',
      exclude: 'add',
      constant: true
    },
    import_datetime: {
      title: 'Import Date & Time',
      type: 'datetime',
      exclude: 'add',
      constant: true
    },
  } );

  module.addExtraOperation( 'view', {
    title: 'Download Report',
    isIncluded: function( $state, model ) { return model.canDownloadReports(); },
    isDisabled: function( $state, model ) { return !model.viewModel.fileExists; },
    operation: function( $state, model ) { return model.viewModel.downloadReport(); }
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
    'CnBaseViewFactory', 'CnHttpFactory', 'CnSession', 'CnModalMessageFactory', 'CnModalConfirmFactory',
    '$q', '$state',
    function( CnBaseViewFactory, CnHttpFactory, CnSession, CnModalMessageFactory, CnModalConfirmFactory,
              $q, $state ) {
      var object = function( parentModel, root ) {
        var self = this;
        CnBaseViewFactory.construct( this, parentModel, root );
        this.isComplete = false;
        this.fileExists = false;

        this.downloadReport = function () {
          return CnHttpFactory.instance( {
            path: 'apex_deployment/' + self.record.getIdentifier(),
            format: 'jpeg'
          } ).file();
        };

        // transitions to the next available deployment for analysis
        this.transitionOnNextViewState = function( any ) {
          if( angular.isUndefined( any ) ) any = false;

          var where = [ { column: 'status', operator: '=', value: 'pending' } ];
          if( !any ) where.push( { column: 'apex_scan.type', operator: '=', value: this.record.scan_type_type } );

          return CnHttpFactory.instance( {
            path: 'apex_host/' + self.record.apex_host_id + '/apex_deployment',
            // get the highest priority record
            data: {
              select: { column: [ 'id', { table: 'first_apex_exam', column: 'barcode' } ] },
              modifier: {
                where: where,
                order: [
                  { 'apex_scan.priority': true },
                  { 'apex_exam.rank': false },
                  { 'first_apex_exam.barcode': false }
                ],
                limit: 1
              }
            }
          } ).get().then( function( response ) {
            if( 0 < response.data.length )
              return $state.go( 'apex_deployment.view', { identifier: response.data[0].id } );
          } );
        };

        // customize the scan list heading
        this.deferred.promise.then( function() {
          if( angular.isDefined( self.apexDeploymentModel ) )
            self.apexDeploymentModel.listModel.heading = 'Sibling Apex Deployment List';
        } );

        this.onView = function( force ) {
          self.isComplete = false;
          self.fileExists = false;
          return this.$$onView( force ).then( function() {
            // do not allow exported deployments to be edited
            self.parentModel.getEditEnabled = null == self.record.status || 'exported' == self.record.status
                                            ? function() { return false; }
                                            : function() { return self.parentModel.$$getEditEnabled(); };

            return self.parentModel.metadata.getPromise().then( function() {
              // get a limited list of code types which apply to this deployment's scan type
              self.codeTypeList = self.parentModel.metadata.codeTypeList.filter(
                codeType => 0 <= codeType.scan_type_id_list.indexOf( self.record.scan_type_id )
              );

              self.isComplete = true;
              self.codeTypeList.forEach( function( codeType ) { self.record['codeType'+codeType.id] = false; } );
              return $q.all( [
                // set all code values to false then get the scan's codes
                CnHttpFactory.instance( {
                  path: 'apex_deployment/' + self.record.getIdentifier() + '/code',
                  data: { select: { column: [ 'code_type_id' ] } }
                } ).query().then( function( response ) {
                  response.data.forEach( function( code ) {
                    self.record['codeType'+code.code_type_id] = true;
                  } );
                } ),

                // determine whether the report is available
                CnHttpFactory.instance( {
                  path: 'apex_deployment/' + self.record.getIdentifier() + '?report=1'
                } ).get().then( function( response ) {
                  self.fileExists = response.data;
                } )
              ] );
            } );
          } );
        };

        this.patch = function( property ) {
          if( property.match( /^codeType/ ) ) {
            var newValue = self.record[property];
            var codeTypeId = property.replace( /^codeType/, '' );
            if( newValue ) {
              return CnHttpFactory.instance( {
                path: 'code',
                data: {
                  apex_deployment_id: self.record.getIdentifier(),
                  code_type_id: codeTypeId,
                  user_id: CnSession.user.id
                },
                onError: function( response ) {
                  // ignore 409 (code already exists)
                  if( 409 != response.status ) {
                    self.record[property] = !self.record[property];
                    CnModalMessageFactory.httpError( response );
                  }
                }
              } ).post()
            } else {
              return CnHttpFactory.instance( {
                path: 'code/apex_deployment_id=' + self.record.getIdentifier() + ';code_type_id=' + codeTypeId,
                onError: function( response ) {
                  // ignore 404 (code has already been deleted)
                  if( 404 != response.status ) {
                    self.record[property] = !self.record[property];
                    CnModalMessageFactory.httpError( response );
                  }
                }
              } ).delete();
            }
          } else {
            var promiseList = [];
            var codeList = [];
            var resetCodes = false;

            // check if pass is being set to null (which at this point will be an empty string)
            if( 'pass' == property && '' === self.record.pass ) {
              // gather all selected code types
              for( var column in self.record )
                if( column.match( /^codeType/ ) && self.record[column] )
                  codeList.push( column );

              // if there are any code types, offer to reset them
              if( 0 < codeList.length ) {
                promiseList.push(
                  CnModalConfirmFactory.instance( {
                    title: 'Reset Codes',
                    message: 'Do you also wish to remove all codes associated with this deployment?'
                  } ).show().then( function( response ) { resetCodes = response; } )
                );
              }
            }

            return $q.all( promiseList ).then( function() {
              var data = {};
              data[property] = self.record[property];
              if( resetCodes ) data.reset_codes = true;
              return self.onPatch( data ).then( function() {
                if( 'pass' == property ) return self.onView();
              } );
            } );
          }
        };
      };
      return { instance: function( parentModel, root ) { return new object( parentModel, root ); } };
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.factory( 'CnApexDeploymentModelFactory', [
    'CnBaseModelFactory',
    'CnApexDeploymentAddFactory', 'CnApexDeploymentListFactory', 'CnApexDeploymentViewFactory',
    'CnSession', 'CnHttpFactory', '$q',
    function( CnBaseModelFactory,
              CnApexDeploymentAddFactory, CnApexDeploymentListFactory, CnApexDeploymentViewFactory,
              CnSession, CnHttpFactory, $q ) {
      var object = function( root ) {
        var self = this;
        CnBaseModelFactory.construct( this, module );
        this.addModel = CnApexDeploymentAddFactory.instance( this );
        this.listModel = CnApexDeploymentListFactory.instance( this );
        this.viewModel = CnApexDeploymentViewFactory.instance( this, root );

        this.canDownloadReports = function() { return 3 <= CnSession.role.tier; };

        // extend getServiceData
        this.getServiceData = function( type, columnRestrictLists ) {
          var data = this.$$getServiceData( type, columnRestrictLists );
          if( 'apex_deployment' == this.getSubjectFromState() && 'view' == this.getActionFromState() )
            data.sibling_apex_deployment_id = self.getQueryParameter( 'identifier' );
          return data;
        };

        // extend getMetadata
        this.getMetadata = function() {
          return this.$$getMetadata().then( function() {
            return $q.all( [
              CnHttpFactory.instance( {
                path: 'apex_host',
                data: {
                  select: { column: [ 'id', 'name' ] },
                  modifier: { order: { name: false } }
                }
              } ).query().then( function success( response ) {
                self.metadata.columnList.apex_host_id.enumList = [];
                response.data.forEach( function( item ) {
                  self.metadata.columnList.apex_host_id.enumList.push( {
                    value: item.id,
                    name: item.name
                  } );
                } );
              } ),

              CnHttpFactory.instance( {
                path: 'code_type',
                data: {
                  select: { column: [ 'id', 'code', 'description', 'scan_type_id_list' ] },
                  modifier: { order: 'code' }
                }
              } ).query().then( function( response ) {
                // convert the id list into an array if integers
                response.data.forEach(
                  item => item.scan_type_id_list = item.scan_type_id_list.split( ',' ).map( id => parseInt(id) )
                );
                self.metadata.codeTypeList = response.data;
              } )
            ] );
          } );
        };
      };

      return {
        root: new object( true ),
        instance: function() { return new object( false ); }
      };
    }
  ] );

} );
