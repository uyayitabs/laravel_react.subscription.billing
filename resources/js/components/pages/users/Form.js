import React from "react";
import Select from "react-select";
import { toastr } from "react-redux-toastr";

import { Button, Modal, ModalBody, ModalFooter, ModalHeader, Col } from "reactstrap";
import { AvForm, AvGroup, AvInput } from "availity-reactstrap-validation";

import { AddUser, UpdateUser } from "../../controllers/users"

class UserForm extends React.Component {
  constructor(props) {
    super(props);

    let type;

    if (this.props.selectedData) {
        type = 'Edit';
    } else {
        type = 'Add';
    }
    
    this.state = {
      formType: type,
      formName: 'User',
      id: null,
      username: null,
      email: null,
      password: null,
      password_confirmation: null
    };

    this.toggle = this.toggle.bind(this);
    this.handleChange = this.handleChange.bind(this);
    this.handleSubmit = this.handleSubmit.bind(this);
  }

  toggle() {
    this.props.hide()
  }

  update(val) {
    this.props.update(val)
  }

  handleSelectChange = (name, value) => {
    let user = Object.assign({}, this.state.user)
    user[name] = value.value

    this.setState({
      user,
      [name]: value
    })
  }

  handleChange(e) {
    this.setState({ [e.target.name]: e.target.value });
  }
  
  handleSubmit(e) {
    e.preventDefault();

    let user = null;

    (async () => {      
      if (this.props.selectedData) {
        user = {
          username: this.state.username,
          email: this.state.email,
          _method: 'PATCH'
        }

        if (this.state.password !== '' || this.state.password_confirmation !== '') {
          user.password = this.state.password;
          user.password_confirmation = this.state.password_confirmation;

          if (user.password !== user.password_confirmation) {
            return this.showToastr(404, 'Error', 'Passwords do not match.');
          }
        }
        
        await UpdateUser(user, this.props.selectedData.id)
          .then(res => {
            this.toggle()
            this.props.update(res.data.data, res.data.data.id)

            this.showToastr(201, 'Success', 'User has been updated.');
          })
          .catch(err => {
            this.showToastr(404, 'Error', 'Username/Email has already been taken.');
          }); 
      } else {
        user = {
          username: this.state.username,
          email: this.state.email,
          password: this.state.password,
          password_confirmation: this.state.password_confirmation
        }
        
        await AddUser(user)
          .then(res => {
            const user = JSON.parse(res.config.data);
            this.toggle()
            this.props.update(user)

            this.showToastr(201, 'Success', res.data.message);
          })
          .catch(err => {
            let msg = '';

            if (err.response.data.errors) {
              if (err.response.data.errors.email) {
                msg = err.response.data.errors.email[0];
              } else {
                msg = err.response.data.errors.password[0];
              }
            } else {
              msg = 'The username has already been taken.';
            }

            this.showToastr(404, 'Error', msg);
          }); 
      }
    })()
    .catch(err => {
      console.log(err)
    })
  }
  
  showToastr(type, title, msg) {
    const opts = {
      timeOut: 5000,
      showCloseButton: false,
      progressBar: false
    };

    const toastrInstance =
      type === 201 ? toastr.success
      : toastr.error;

    toastrInstance( title, msg, opts );
  }

  componentWillMount() {
    console.log(this.props.selectedData)
    if (this.props.selectedData) {
      const user = this.props.selectedData;

      this.setState({
        id: user.id,
        username: this.props.selectedData.id && this.props.selectedData.username ? this.props.selectedData.username : null,
        email: this.props.selectedData.id && this.props.selectedData.person.email ? this.props.selectedData.person.email : null,
        password: '',
        password_confirmation: ''
      })
    }
  }

  render() {
    return (
      <Modal
          isOpen={ this.props.show }
          toggle={ this.toggle }
          centered
        >
          <AvForm onSubmit={ this.handleSubmit }>
            <ModalHeader>{ this.state.formType} { this.state.formName }</ModalHeader>
            <ModalBody className="mt-3 mb-3">
              <AvGroup className="row">
                <Col>
                  <AvInput
                    id="username"
                    name="username"
                    placeholder="Username"
                    value={ this.state.username }
                    onChange={ this.handleChange }
                  />
                </Col>
                <Col>
                  <AvInput
                    id="email"
                    name="email"
                    placeholder="Email"
                    value={ this.state.email }
                    onChange={ this.handleChange }
                    disabled={ this.props.selectedData && this.props.selectedData.person ? true : false }
                  />
                </Col>
                <Col>
                  <Select
                    id="role"
                    className="react-select-container"
                    classNamePrefix="react-select"
                    placeholder="Role Type"
                    options={this.state.roleOpts}
                    value={this.state.role}
                    onChange={this.handleSelectChange.bind(this, 'role')}
                  />
                </Col>
              </AvGroup>
              {/* <AvGroup className="row">
                <Col>
                  <AvInput
                    id="password"
                    type="password"
                    name="password"
                    placeholder="Password"
                    value={ this.state.password }
                    onChange={ this.handleChange }
                  />
                </Col>
                <Col>
                  <AvInput
                    id="password_confirmation"
                    type="password"
                    name="password_confirmation"
                    placeholder="Password Confirmation"
                    value={ this.state.password_confirmation }
                    onChange={ this.handleChange }
                  />
                </Col>
              </AvGroup> */}
            </ModalBody>
            <ModalFooter className="justify-content-between">
              <span className="btn btn-danger" onClick={ this.toggle }>Cancel</span>
              <Button color="primary">Submit</Button>
            </ModalFooter>
          </AvForm>
        </Modal>
    );
  }
}

export default UserForm;
