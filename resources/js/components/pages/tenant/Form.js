import React from "react";
import { withRouter } from 'react-router-dom';
import Select from "react-select";

import { Button, Modal, ModalBody, ModalFooter, ModalHeader, Label, FormGroup, Input, Row, Col } from "reactstrap";
import { AvForm, AvField, AvGroup } from "availity-reactstrap-validation";
import { toastr } from "react-redux-toastr";
import Loader from '../../components/Loader';


import { GetTenantLists, GetTenant, GetMyTenants, AddTenant, UpdateTenant } from '../../controllers/tenants';

class TenantForm extends React.Component {
  constructor(props) {
    super(props);

    const path = window.location.href.split('/');

    this.state = {
      formType: null,
      formName: 'Tenant',
      id: null,
      parent_id: null,
      parent_tenant: {
        value: null,
        label: null
      },
      billing_day: null,
      logo: null,
      loading: false
    };

    this.name = React.createRef();
    this.toggle = this.toggle.bind(this);
    this.handleInputChange = this.handleInputChange.bind(this);
    this.handleSubmit = this.handleSubmit.bind(this);
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

    toastrInstance(title, msg, opts);
  }

  toggle() {
    this.props.hide()
  }

  handleSelectChange = (name, value) => {
    let change = {};
    change[name] = value;
    this.setState(change);
  }

  handleInputChange(e) {
    this.setState({ [e.target.name]: e.target.value });
    e.persist();
  }

  handleFileChange(files) {
    const file = files[0]
    let reader = new FileReader()
    reader.readAsDataURL(file)

    reader.onload = () => {
      const b64Data = reader.result;
      fetch(b64Data)
        .then(res => res.blob())
        .then(blob => this.setState({ logo: blob }))
    }
    reader.onerror = function (err) {
      console.log(err);
    }
  }

  handleSubmit(e) {
    e.persist();

    if (this.state.id) {
      (async () => {
        const getTenant = {
          _method: 'PATCH',
          id: this.state.id,
          name: this.state.name,
          parent_id: this.state.id === 1 ? null : this.state.parent_tenant.value,
          billing_day: this.state.billing_day
        }

        await UpdateTenant(this.props.id, getTenant)
          .then(res => {
            this.props.update(res.data.data)
          })
          .catch(err => {
            this.showToastr(err.response.status, 'Error', err.response.data.message);
          });
      })()
        .catch(err => {
          console.log(err)
        })
    } else {
      if (this.state.name !== null && this.state.parent_tenant.value !== null) {
        (async () => {
          const newTenant = {
            name: this.state.name,
            parent_id: this.state.parent_tenant.value,
            billing_day: this.state.billing_day
          }

          await AddTenant(newTenant)
            .then(res => {
              this.props.history.push({ pathname: `/tenants/${res.data.data.id}/details` })
            })
            .catch(err => {
              this.showToastr(err.response.status, 'Error', err.response.data.message);
            });
        })()
          .catch(err => {
            console.log(err)
          })
      } else {
        return false;
      }
    }
  }

  componentDidMount() {
    (async () => {
      let tenantOpts = [];

      await GetMyTenants()
        .then(res => {
          const tenants = res.data.data;

          tenants.forEach((tenant) => {
            tenantOpts.push({
              label: tenant.name,
              value: tenant.id
            });
          });

          this.setState({
            tenantOpts
          });
        })
        .catch(err => {
          this.showToastr(err.response.status, 'Error', err.response.data.message);
        });

      if (this.props.id) {
        (async () => {
          await GetTenant(null, this.props.id)
            .then(res => {
              const tenant = res.data.data;
              const selectedRootTenant = tenantOpts.filter(v => v.value === tenant.parent_id);

              this.setState({
                parent_tenant: selectedRootTenant.length > 0 ? selectedRootTenant[0] : null,
                name: tenant.name,
                id: tenant.id,
                parent_id: selectedRootTenant.length > 0 ? selectedRootTenant[0].value : null,
                billing_day: selectedRootTenant.length > 0 ? tenant.billing_day : null,
                formType: 'Edit',
                loading: false
              });
            })
            .catch(err => {
              this.showToastr(err.response.status, 'Error', err.response.data.message);
            });
        })()
          .catch(err => {
            console.log(err)
          })
      } else {
        this.setState({
          formType: 'Add New',
          loading: false
        })
      }
    })()
      .catch(err => {
        console.log(err)
      })
  }

  render() {
    return (
      <Modal
        isOpen={this.props.show}
        toggle={this.toggle}
        centered
      >
        {!this.state.loading ?
          <AvForm onSubmit={this.handleSubmit}>
            <ModalHeader>{this.state.formType} {this.state.formName}</ModalHeader>
            <ModalBody className="mt-3 mb-3">
              <Row>
                <Col md={2}>Tenant:</Col>
                <Col md={10}>
                  <AvGroup className="row">
                    <Col>
                      <AvField
                        name="name"
                        label="Name"
                        value={this.state.name}
                        onChange={this.handleInputChange}
                        required
                        ref={this.props.inputElement}
                      />
                    </Col>
                    <Col>
                      <Label for="parent_tenant">Parent Tenant</Label>
                      {this.state.tenantOpts ?
                        <Select
                          id="parent_tenant"
                          className="react-select-container"
                          classNamePrefix="react-select"
                          options={this.state.tenantOpts}
                          value={this.state.parent_tenant}
                          onChange={this.handleSelectChange.bind(this, 'parent_tenant')}
                          maxMenuHeight="100"
                        /> : null
                      }
                    </Col>
                  </AvGroup>
                  <AvGroup>
                    <AvField
                      name="billing_day"
                      label="Invoice Billing day (day of the month when invoice are processed, e.g. every 15th of the month)"
                      value={this.state.billing_day}
                      onChange={this.handleInputChange}
                      required
                      ref={this.props.inputElement}
                    />
                  </AvGroup>
                  <FormGroup>
                    <Label>Logo</Label>
                    <Input
                      type="file"
                      name="file"
                      onChange={ (e) => this.handleFileChange(e.target.files) }
                    />
                  </FormGroup>
                </Col>
              </Row>
            </ModalBody>
            <ModalFooter className="justify-content-between">
              <span className="btn btn-danger" onClick={this.toggle}>Cancel</span>
              <Button color="primary">Submit</Button>
            </ModalFooter>
          </AvForm>
          : <Loader />
        }
      </Modal>
    );
  }
}

export default withRouter(TenantForm);
