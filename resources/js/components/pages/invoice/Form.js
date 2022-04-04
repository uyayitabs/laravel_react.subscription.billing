import React from "react";
import { withRouter} from 'react-router-dom';
import { toastr } from "react-redux-toastr";
import _ from "lodash";

import Select from "react-select";
import { Button, Modal, ModalBody, ModalFooter, ModalHeader, Label, FormGroup, Table, Row, Col } from "reactstrap";
import { AvForm, AvField, AvGroup, AvInput } from "availity-reactstrap-validation";
import { GetRelations, GetRelation } from '../../controllers/relations';
import Loader from '../../components/Loader';
import { GetInvoice, AddInvoice, UpdateInvoice } from '../../controllers/invoices';

class SalesInvoiceForm extends React.Component {
  constructor(props) {
    super(props);

    const path = window.location.href.split('/');
    const id = path[path.length - 2]

    this.state = {
      formType: null,
      formName: 'Invoice',
      id: id,
      description: null,
      sales_invoice: null,
      relation: null,
      relations: [],
      loading: true
    };

    this.relation = React.createRef();
    this.toggle = this.toggle.bind(this);
    this.handleInputChange = this.handleInputChange.bind(this);
    this.handleSubmit = this.handleSubmit.bind(this);
    this.getFullname = this.getFullname.bind(this);
    this.getAddress = this.getAddress.bind(this);
  }

  showToastr(type, title, msg) {
    const opts = {
      timeOut: 5000,
      showCloseButton: false,
      progressBar: false
    };

    const toastrInstance =
      type === 201  ? toastr.success
      : toastr.error;

    toastrInstance( title, msg, opts );
  }
  

  toggle() {
    this.props.hide()
  }

  update(val) {
    this.props.update(val)
  }

  handleSelectChange = (name, value) => {
    let change = {};
    change[name] = value;
    this.setState(change);
    this.updateInvoiceData(change[name].value);
}

  updateInvoiceData = (relation_id) => {
    (async () => {
        await GetRelation(null, relation_id)
            .then(res => {
              var relation_data = res.data.data;

              // Get address data
              var addresses = _.result(relation_data, ['addresses']);
              
              // Contact (Shipping Address)
              var shipping_address = _.filter(addresses, _.matches({ 'address_type_id' : 1}))[0];

              // Billing (Billing Address)
              var billing_address = _.filter(addresses, _.matches({ 'address_type_id' : 3}))[0];

              this.state.sales_invoice.relation_id = relation_data.id;
              this.state.sales_invoice.shipping_address = shipping_address;
              this.state.sales_invoice.billing_address = billing_address;
              //this.state.sales_invoice.shipping_address_id = billing_address.id;
              
              
              UpdateInvoice(
                this.state.sales_invoice.id, 
                {
                  _method: 'PATCH',
                  relation_id: relation_data.id,
                  invoice_address_id: shipping_address.id,
                  shipping_address_id: billing_address.id,
                })
                .then(res => {
                  let sales_invoice = res.data.data;
                  this.setState({ sales_invoice : sales_invoice });
                })
              .catch(err => {
                  console.log(err)
              }); 
            })
        .catch(err => {
            console.log(err)
        })
    })()
    .catch(err => {
        console.log(err)
    })
  }

  handleInputChange(e) {
    this.setState({ [e.target.name]: e.target.value });
    e.persist();
  }

  handleSubmit(e) {
    e.persist();
    const params = {
      _method: 'PATCH',
      relation_id: this.state.sales_invoice.relation_id,
      invoice_address_id: this.state.sales_invoice.invoice_address_id,
      shipping_address_id: this.state.sales_invoice.shipping_address_id,
      description: this.state.description
    }

    UpdateInvoice( this.state.sales_invoice.id, params)
      .then(res => {
        let sales_invoice = res.data.data;
        this.setState({ sales_invoice : sales_invoice });
        this.toggle();
      })
      .catch(err => {
        console.log(err)
      });    
  }

  getFullname(person) {
      return (person.first_name ? person.first_name : '') + ' ' + (person.middle_name ? person.middle_name : '') + ' ' + (person.last_name ? person.last_name : '');
  }

  getAddress(address) {
    return address.street1 + ' ' +
        (address.street2 ? address.street2 : '') + ' ' +
        address.city + ' ' +
        address.zipcode + ' ' +
        (address.country ? address.country.name : '');
}

componentDidMount(){
  this.setState({
    formType: 'Add New',
    sales_invoice: null,
    relation: null,
    relations: [],
  });
  (async () => {
    await Promise.all([
      GetRelations()
        .then(res => {
            const relations = res.data.data;
            let relationNames = [];
            relations.forEach((relation) => {
              if (relation.persons.length > 0 ) { 
                relationNames.push({
                    label: relation.customer_number + ' / ' + this.getFullname(relation.persons[0]),
                    value: relation.id
                });
              }
            });
            this.setState({
              relations: relationNames,
              loading: false
            });

            GetInvoice(this.state.id)
              .then(res => {
                let sales_invoice = res.data.data;
                this.setState({
                  sales_invoice : sales_invoice,
                  relation: { 
                    label: sales_invoice.relation.customer_number + ' / ' + this.getFullname(sales_invoice.relation.persons[0]),
                    value: sales_invoice.relation_id
                  },
                  description : sales_invoice
                });
                this
              })
              .catch(err => {
                console.log(err)
            })
        })
        .catch(err => {
            console.log(err)
        }),
    ])
  })()
  .catch(err => {
      console.log(err)
  })
}

  render() {
    return (
      <Modal
          isOpen={ this.props.show }
          toggle={ this.toggle }
          centered
        >
          <AvForm onSubmit={ this.handleSubmit }>
            <ModalHeader>{ this.state.formType } { this.state.formName }</ModalHeader>
            <ModalBody className="mt-3 mb-3">
            { !this.state.loading ?
            <Row>
              <Col md={2}>Invoice:</Col>
              <Col md={10}>
                <FormGroup>
                  <Col style={{ fontWeight: 'bold' }}>
                    Invoice Number
                  </Col>
                  <Col>
                    { this.state.sales_invoice ? this.state.sales_invoice.invoice_no : '' }
                  </Col>
                </FormGroup>
                <FormGroup>
                  <Col style={{ fontWeight: 'bold' }}>
                    Invoice Date:
                  </Col>
                  <Col>
                    { this.state.sales_invoice ? this.state.sales_invoice.invoice_no : '' }
                  </Col>
                </FormGroup>
                <FormGroup>
                  <Col style={{ fontWeight: 'bold' }}>
                    Description:
                  </Col>
                  <Col>
                    <AvGroup>
                      <AvField type="textarea" name="description" placeholder="Description" value={ this.state.sales_invoice ? this.state.sales_invoice.description : '' } onChange={ this.handleInputChange } required ref={this.props.inputElement} />
                    </AvGroup>
                  </Col>
                </FormGroup>
                <FormGroup>
                  <Col style={{ fontWeight: 'bold' }}>
                    Customer:
                  </Col>
                  <Col>
                  <Select
                      required
                      id="relation"
                      className="react-select-container"
                      classNamePrefix="react-select"
                      placeholder="Customer"
                      options={this.state.relations}
                      value={this.state.relation}
                      onChange={ this.handleSelectChange.bind(this, 'relation') }
                      maxMenuHeight="100"
                    />
                  </Col>
                </FormGroup>
                <FormGroup>
                  <Col style={{ fontWeight: 'bold' }}>
                    Shipping Address:
                  </Col>
                  <Col>
                    { this.state.sales_invoice ? this.getAddress(this.state.sales_invoice.shipping_address.city.name) : '' }
                  </Col>
                </FormGroup>
                <FormGroup>
                  <Col style={{ fontWeight: 'bold' }}>
                    Billing Address:
                  </Col>
                  <Col>
                    { this.state.sales_invoice ? this.getAddress(this.state.sales_invoice.invoice_address) : '' }
                  </Col>
                </FormGroup>
              </Col>
            </Row>
             :<Loader />
            }
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

export default withRouter(SalesInvoiceForm);
