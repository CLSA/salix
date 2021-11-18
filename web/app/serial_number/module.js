cenozoApp.defineModule( { name: 'serial_number', models: 'list', create: module => {

  angular.extend( module, {
    identifier: {},
    name: {
      singular: 'serial number',
      plural: 'serial numbers',
      possessive: 'serial number\'s'
    },
    columnList: {
      id: {
        title: 'Serial Number'
      },
      site: {
        column: 'site.name',
        title: 'Site'
      }
    },
    defaultOrder: {
      column: 'site.name',
      reverse: false
    }
  } );

  /* ######################################################################################################## */
  cenozo.providers.factory( 'CnSerialNumberModelFactory', [
    'CnBaseModelFactory', 'CnSerialNumberListFactory',
    function( CnBaseModelFactory, CnSerialNumberListFactory ) {
      var object = function( root ) {
        var self = this;
        CnBaseModelFactory.construct( this, module );
        this.listModel = CnSerialNumberListFactory.instance( this );
        this.getViewEnabled = function() { return false; };
      };

      return {
        root: new object( true ),
        instance: function() { return new object( false ); }
      };
    }
  ] );

} } );
