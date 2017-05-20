(function(angular) {
    'use strict';

    angular.module('wptApp').factory('BaseCollection', BaseCollectionFactory);

    BaseCollectionFactory.$inject = ['BaseObservable'];
    function BaseCollectionFactory(BaseObservable) {
        function BaseCollection() {
        };
        BaseCollection.prototype = angular.extend({}, BaseObservable.prototype);

        var arraySlice = Array.prototype.slice;
        BaseCollection.fromArrayGenerator = function(thisClass) {
            thisClass.prototype.slice = function(begin, end) {
                var copy = angular.extend([], thisClass.prototype, new thisClass);
                arraySlice.call(this, begin, end).forEach(function(item) {
                    this.push(item);
                }, copy);
                return copy;
            };

            return function(items) {
                var me = angular.extend([], thisClass.prototype, new thisClass);
                if (typeof items === 'undefined') {
                    return me;
                }
                items.forEach(function(item) {
                    var createdItem = this.createFromObject(item);
                    this.push(createdItem);
                    this.trigger('fromArray:add:item', createdItem);
                    this.trigger('changed:item', createdItem);
                }, me);
                return me;
            };
        };
        BaseCollection.prototype.create = function(title) {
            throw new Error('BaseCollection create not implemented');
        };
        BaseCollection.prototype.createFromObject = function(object) {
            return angular.extend(this.create(), object);
        };
        BaseCollection.prototype.addNew = function() {
            var item = this.create.apply(this, arguments);
            return this.add(item);
        };
        BaseCollection.prototype.add = function(item) {
            this.push(item);
            this.trigger('add:item', item);
            this.trigger('changed:item', item);
            return item;
        };
        BaseCollection.prototype.forEachLive = function(callback) {
            this.forEach(callback);
            this.on('add:item', function(item) {
                callback.call(item, item, this.length);
            });
        };
        BaseCollection.prototype.remove = function($index) {
            var item = this[$index];
            item.trigger('remove', $index);
            this.trigger('remove:item', item, $index);
            this.splice($index, 1);
            this.trigger('changed:item', item);
            return this;
        };
        /**
         * Implement null-object getter
         * @param {Number} $index
         * @return {Item}
         */
        BaseCollection.prototype.getOrNull = function($index) {
            var item = this[$index];
            if (typeof item === 'undefined') {
                return this.createFromObject({});
            }
            return item;
        };
        BaseCollection.prototype.copy = function() {
            var copy = this.fromArray([]);
            this.forEach(function(item) {
                copy.add(item.copy());
            });
            return copy;
        };
        return BaseCollection;
    };
})(angular);
