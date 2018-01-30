define( function() {
  'use strict';

  try { var module = cenozoApp.module( 'apex_deployment', true ); } catch( err ) { console.warn( err ); return; }
  angular.extend( module, {
    identifier: {
      parent: {
        subject: 'apex_scan',
        column: 'apex_scan.id'
      }
    },
    name: {
      singular: 'apex deployment',
      plural: 'apex deployments',
      possessive: 'apex deployment\'s',
      pluralPossessive: 'apex deployments\''
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
    }
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
    'CnBaseViewFactory', 'CnHttpFactory', 'CnSession', 'CnModalMessageFactory',
    function( CnBaseViewFactory, CnHttpFactory, CnSession, CnModalMessageFactory ) {
      var object = function( parentModel, root ) {
        var self = this;
        CnBaseViewFactory.construct( this, parentModel, root );
        this.isComplete = false;

        // customize the scan list heading
        this.deferred.promise.then( function() {
          if( angular.isDefined( self.apexScanModel ) )
            self.apexScanModel.listModel.heading = 'Sister Apex Scan List';
        } );

        this.onView = function( force ) {
          return this.$$onView( force ).then( function() {
            // set all code values to false then get the scan's codes
            return self.parentModel.metadata.getPromise().then( function() {
              self.isComplete = true;
              self.parentModel.metadata.codeTypeList.forEach( function( codeType ) {
                self.record['codeType'+codeType.id] = false;
              } );
              return CnHttpFactory.instance( {
                path: 'apex_deployment/' + self.record.id + '/code',
                data: { select: { column: [ 'code_type_id' ] } }
              } ).query().then( function( response ) {
                response.data.forEach( function( code ) {
                  self.record['codeType'+code.code_type_id] = true;
                } );
              } );
            } );
          } );
        };

        this.patchCodeType = function( codeTypeId ) {
          var newValue = self.record['codeType'+codeTypeId];
          if( newValue ) {
            return CnHttpFactory.instance( {
              path: 'code',
              data: {
                apex_deployment_id: self.record.id,
                code_type_id: codeTypeId,
                user_id: CnSession.user.id
              },
              onError: function( response ) {
                // ignore 409 (code already exists)
                if( 409 != response.status ) CnModalMessageFactory.httpError( response );
              }
            } ).post()
          } else {
            return CnHttpFactory.instance( {
              path: 'code/apex_deployment_id='+self.record.id+';code_type_id='+codeTypeId,
              onError: function( response ) {
                // ignore 404 (code has already been deleted)
                if( 404 != response.status ) CnModalMessageFactory.httpError( response );
              }
            } ).delete();
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
    'CnHttpFactory', '$q',
    function( CnBaseModelFactory,
              CnApexDeploymentAddFactory, CnApexDeploymentListFactory, CnApexDeploymentViewFactory,
              CnHttpFactory, $q ) {
      var object = function( root ) {
        var self = this;
        CnBaseModelFactory.construct( this, module );
        this.addModel = CnApexDeploymentAddFactory.instance( this );
        this.listModel = CnApexDeploymentListFactory.instance( this );
        this.viewModel = CnApexDeploymentViewFactory.instance( this, root );

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
                  select: { column: [ 'id', 'code', 'description' ] },
                  modifier: { order: 'code' }
                }
              } ).query().then( function( response ) {
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
