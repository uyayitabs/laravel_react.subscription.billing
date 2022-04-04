import React, { Component } from "react";
import ReactDOM from 'react-dom';
import { Provider } from "react-redux";
import ReduxToastr from "react-redux-toastr";

import store from "./redux/store/index";
import Routes from "./routes/Routes";

class App extends Component {
    render () {
        return (
            <Provider store={store}>
                <Routes />
                <ReduxToastr
                timeOut={5000}
                newestOnTop={true}
                position="top-right"
                transitionIn="fadeIn"
                transitionOut="fadeOut"
                progressBar
                closeOnToastrClick
                />
            </Provider>
        )
    }
}

ReactDOM.render(<App />, document.getElementById('app'))