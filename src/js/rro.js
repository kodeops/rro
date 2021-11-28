'use strict';

const RRO_SUCCESS_KEY = 'response';
const RRO_ERROR_KEY = 'error';
const RRO_TYPE_KEY = 'type';

module.exports = class RichResponseObject {
     constructor(response) {
        this.response = response;
        if (! this.isValid()) {
            return;
        }
        this.type = this.getType();
        this.message = this.getMessage();
        this.data = this.getData();
    }

    // Getters

    getMessage() {
        if (this.isSuccess()) {
            return this.response[RRO_SUCCESS_KEY].message;
        }

        if (this.isError()) {
            return this.response[RRO_ERROR_KEY].message;
        }
    }

    getType() {
        if (this.isSuccess()) {
            return this.response[RRO_SUCCESS_KEY].type;
        }

        if (this.isError()) {
            return this.response[RRO_ERROR_KEY].type;
        }
    }

    getData() {
        if (this.isSuccess()) {
            if (! this.response[RRO_SUCCESS_KEY].hasOwnProperty("data")) {
                return;
            }

            return this.response[RRO_SUCCESS_KEY].data;
        }

        if (this.isError()) {
            if (! this.response[RRO_ERROR_KEY].hasOwnProperty("data")) {
                return;
            }

            return this.response[RRO_ERROR_KEY].data;
        }
    }

    getDataKey(key) {
        this.getData();

        if (this.data === undefined) {
            return;
        }

        if (this.data.hasOwnProperty(key)) {
            return this.data[key];
        }
    }

    // Methods

    isValid() {
        if (
            ! this.isSuccess() && 
            ! this.isError()
        ) {
            return false;
        }

        if (! this.getType()) {
            return false;
        }

        return true;
    }

    isSuccess() {
        if (this.response.hasOwnProperty(RRO_SUCCESS_KEY)) {
            return true;
        }

        return false;
    }

    isError() {
        if (this.response.hasOwnProperty(RRO_ERROR_KEY)) {
            return true;
        }

        return false;
    }

    isType(type) {
        return this.getType() == type;
    }
}