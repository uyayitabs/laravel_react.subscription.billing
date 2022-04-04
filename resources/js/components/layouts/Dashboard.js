import React from "react";
import { withRouter } from "react-router";

import withAuth from '../services/withAuth';

import Wrapper from "../components/Wrapper";
import Sidebar from "../components/Sidebar";
import Main from "../components/Main";
import Navbar from "../components/Navbar";
import Content from "../components/Content";
import Footer from "../components/Footer";

import Error from '../pages/auth/Error';
import AuthService from '../services/authService';

const Auth = new AuthService();

class Dashboard extends React.Component {
  isMounted = false;

  constructor(props) {
    super(props)

    this.state = {
      children: this.props.children,
      pathname: null,
      isError: false,
      error: null
      // email: this.props.user.email
    }

    this.checkHasError = this.checkHasError.bind(this)
    this.handleLeavePage = this.handleLeavePage.bind(this)
  }

  checkHasError() {
    if (this.isMounted) {
      if (!Auth.checkIsError()) {
        setTimeout(this.checkHasError, 100)
      } else {
        const error = JSON.parse(sessionStorage.getItem('error'))
        this.setState({
          isError: true,
          error
        })
      }
    }
  }

  handleLeavePage() {
    this.isMounted = false;
    Auth.updateError(false)
  }

  componentDidMount() {
    this.isMounted = true;
    this.checkHasError()
    this.setState({ pathname: this.props.history.location.pathname })

    window.addEventListener('beforeunload', this.handleLeavePage)
  }

  componentWillUnmount() {
    window.removeEventListener('beforeunload', this.handleLeavePage)
  }

  componentDidUpdate() {
    if (this.state.pathname !== this.props.history.location.pathname) {
      Auth.updateError(false)
      this.setState({ 
        isError: false,
        pathname: this.props.history.location.pathname 
      })
      setTimeout(this.checkHasError, 100)
    }
  }
  
  render() {
    return (
      <React.Fragment>
        <Wrapper>
          <Sidebar />
          
          <Main>
            <Navbar />

            { !this.state.isError ?
              <Content>
                { this.state.children }
              </Content> :
              <Content>
                <Error error={ this.state.error } />
              </Content>
            }            

            <Footer />
          </Main>
        </Wrapper>
      </React.Fragment>
    )
  }
}

export default withRouter(withAuth(props => <Dashboard { ...props } />));
