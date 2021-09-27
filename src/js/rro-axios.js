import axios from 'axios';
import toastr from 'toastr';
import RichResponseObject from './rro.js';

window.http = axios.create();

let token_input_name = 'csrf-token';
let contact_support = "Please try again or contact us if the error keeps reproducing.";
let default_error_message = "Whoops! This should not happen. " + contact_support;
let response_message = "An unhandled situation occurred! " + contact_support;

// Inject CSRF token
let token = document.head.querySelector('meta[name="' + token_input_name + '"]');
if (token) {
    http.defaults.params = {};
    http.defaults.params[token_input_name] = token.value;
}

// Add RichResponseObject interceptors
http.interceptors.response.use(function (response) {
    // Any status code that lie within the range of 2xx cause this function to trigger
    if (response.request.responseURL.includes("signin")) {
        toastr.warning(
            'Redirecting to signing page...', 
            'Your session has expired', 
            {timeOut: 0, positionClass: 'toastr-top-center', progressBar: false}
        );
        setTimeout(function(){
            window.location.href = response.request.responseURL + '?reason=expired';
        }, 1500);

        throw new axios.Cancel('Operation cancelled by an expired session.');
    }

    response.rro = handleRichResponseObjectResponse(response.data, false);
    return response;
}, function (error) {
    // Any status codes that falls outside the range of 2xx cause this function to trigger
    error.rro = handleRichResponseObjectResponse(error.response.data, true);
    return Promise.reject(error);
});

function handleRichResponseObjectResponse(response, isErrorResponse) {
    // Create RichResponseObject object
    let rro = new RichResponseObject(response);

    // Check if response is a valid RichResponseObject
    if (! rro.isValid()) {
        toastr.error(default_error_message);
        if (! isErrorResponse) {
            // If this is not an error response, we should cancel the request
            // because it does not contains a valid RichResponseObject and
            // it will cause the promise to fail as it expects a RichResponseObject.
            throw new axios.Cancel('Operation cancelled by an invalid RichResponseObject response.');
        }
    } else {
        // This is an error, keep the message visible for longer
        let toastr_options = {"showDuration": "15000"};
        
        // If response contains a valid error object, show the RichResponseObject error message
        if (rro.isError()) {
            toastr.error(rro.message, '', toastr_options);
        } else {
            if (isErrorResponse) {
                // If the response does not contain a valid error object, show the default error message
                toastr.error(response_message, '', toastr_options);
            }
        }
    }

    return rro;
}