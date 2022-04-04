import React from "react";
import Select from "react-select";
import DatePicker from "react-datepicker";
import ReactQuill from "react-quill";
import moment from 'moment';

import { Button, Modal, ModalBody, ModalFooter, ModalHeader, FormGroup, CustomInput, Col } from "reactstrap";
import { AvForm, AvGroup, AvInput, AvFeedback } from "availity-reactstrap-validation";

import { AddProduct, UpdateProduct, GetProductTypeList } from '../../controllers/products';
import Loader from '../../components/Loader';

class ProductForm extends React.Component {
  constructor(props) {
    super(props);

    this.state = {
      product: this.props.id ? this.props.id : {},
      formType: '',
      formName: 'Product',
      statusOpts: [
        {
          value: 1,
          label: 'ACTIVE',
        }, {
          value: 0,
          label: 'UNAVAILABLE',
        }, {
          value: 2,
          label: 'DISABLED',
        }
      ],
      loading: true
    }

    this.toggle = this.toggle.bind(this);
    this.handleInputChange = this.handleInputChange.bind(this);
    this.handleQuillChange = this.handleQuillChange.bind(this);
    this.handleDatePickerEvent = this.handleDatePickerEvent.bind(this);
    this.handleSubmit = this.handleSubmit.bind(this);
  }

  toggle() {
    this.props.hide()
  }

  update(val) {
    this.props.update(val)
  }

  handleSelectChange = (name, value) => {
    let product = Object.assign({}, this.state.product)
    product[name] = value.value

    this.setState({
      product,
      [name]: value
    })
  }

  handleCheckChange(e, name) {
    let product = Object.assign({}, this.state.product)
    const { checked } = event.target

    product[name] = checked
    this.setState({ product })
  }

  handleInputChange(e) {
    e.persist();    

    let product = Object.assign({}, this.state.product)
    const { name, value } = event.target

    product[name] = value

    this.setState({ product })
  }

  handleQuillChange(val) {
    let product = Object.assign({}, this.state.product)

    product.description_long = val

    this.setState({ product })
  }

  handleDatePickerEvent(date) {
    let product = Object.assign({}, this.state.product)
    
    product.active_from = moment(date).format('DD-MM-YYYY')
    
    this.setState({ product })
  }

  handleSubmit(e) {
    e.persist()

    const { product } = this.state

    if (this.props.selectedData) {
      // Update
      Object.assign(product, { _method: 'PATCH' });
      (async () => {
        await UpdateProduct(product, product.id)
          .then(res => {
            let getData = res.data.data;

            this.toggle()
            this.props.update(getData, getData.id)
          })
          .catch(err => {
            console.log(err.response)
          });
      })()
        .catch(err => {
          console.log(err)
        })
    } else {
      (async () => {
        await AddProduct(product)
          .then(res => {
            let getData = res.data.data;

            Object.assign(getData, {
              product_type: {
                id: product.product_type_id,
                type: this.state.product_type_id.label
              }
            });
            
            this.toggle()
            this.props.update()
          })
          .catch(err => {
            console.log(err)
          });
      })()
        .catch(err => {
          console.log(err)
        })
    }
  }

  componentDidMount() {
    this.setState({ formType: this.props.selectedData ? 'Edit' : 'Add New' });

    (async () => {
      await GetProductTypeList()
        .then(res => {
          const product_types = res.data.data;
          
          let productOpts = [];
          product_types.forEach((product_type) => {
            productOpts.push({
              label: product_type.name,
              value: product_type.id
            });
          });

          this.setState({ productOpts })

          if (this.props.selectedData) {
            const product = this.props.selectedData,
                  typeId = productOpts.findIndex(item => parseInt(item.value) === parseInt(product.product_type_id)),
                  statusId = this.state.statusOpts.findIndex(item => parseInt(item.value) === parseInt(product.status))

            this.setState({
              product,
              product_type_id: productOpts[typeId],
              status: this.state.statusOpts[statusId]
            })
          }

          this.setState({ loading: false })
        })
        .catch(err => {
          console.log(err)
        });
    })()
      .catch(err => {
        console.log(err)
      })
  }

  render() {
    const { product, loading } = this.state
    return (
      <React.Fragment>
        { !loading ?
          <Modal
            isOpen={this.props.show}
            toggle={this.toggle}
            centered
          >
            <AvForm onSubmit={this.handleSubmit}>
              <ModalHeader>{this.state.formType} {this.state.formName}</ModalHeader>
              <ModalBody className="mt-3 mb-3">
                <FormGroup className="row">
                  <Col>
                    <Select
                      id="product_type_id"
                      className="react-select-container"
                      classNamePrefix="react-select"
                      placeholder="Product Type"
                      options={this.state.productOpts}
                      value={this.state.product_type_id}
                      onChange={this.handleSelectChange.bind(this, 'product_type_id')}
                    />
                  </Col>
                  <Col>
                    <Select
                      id="status"
                      className="react-select-container"
                      classNamePrefix="react-select"
                      placeholder="Status"
                      options={this.state.statusOpts}
                      value={this.state.status}
                      onChange={this.handleSelectChange.bind(this, 'status')}
                    />
                  </Col>
                </FormGroup>
    
                <FormGroup className="row">
                  <Col>
                    <CustomInput 
                      id="serialized"
                      type="checkbox"
                      name="serialized"
                      label="Serialized" 
                      defaultChecked={ product.serialized ? true : false }
                      onChange={ (e) => { this.handleCheckChange(e, 'serialized') } }
                    />
                  </Col>
                  <Col>
                    <AvGroup>
                      <AvInput 
                        id="vendor" 
                        name="vendor" 
                        placeholder="Vendor" 
                        value={product.vendor ? product.vendor : ''} 
                        onChange={this.handleInputChange}
                      />
                    </AvGroup>
                  </Col>
                  <Col>
                    <AvGroup>
                      <AvInput 
                        id="vendor_partcode" 
                        name="vendor_partcode" 
                        placeholder="Vendor Part code" 
                        value={product.vendor_partcode ? product.vendor_partcode : ''} 
                        onChange={this.handleInputChange}
                      />
                    </AvGroup>
                  </Col>
                </FormGroup>
    
                <AvGroup className="row">
                  <Col md={6}>
                    <DatePicker
                      id="active_from"
                      className="form-control"
                      name="active_from"
                      placeholderText="Active from"
                      dateFormat="dd/MM/yyyy"
                      autoComplete="off"
                      selected={product.active_from ? new Date(`${ product.active_from.split('-')[2] }-${ product.active_from.split('-')[1] }-${ product.active_from.split('-')[0] }`) : null}
                      onChange={this.handleDatePickerEvent}
                    />
                  </Col>
                  <Col>
                    <AvInput 
                      id="weight" 
                      name="weight" 
                      placeholder="Weight" 
                      value={product.weight ? product.weight : ''} 
                      onChange={this.handleInputChange}
                    />
                  </Col>
                  <Col>
                    <AvInput 
                      id="ean_code" 
                      name="ean_code" 
                      placeholder="Ean code" 
                      value={product.ean_code ? product.ean_code : ''} 
                      onChange={this.handleInputChange}
                    />
                  </Col>
                </AvGroup>
    
                <AvGroup className="row">
                  <Col>
                    <AvInput 
                      id="price" 
                      name="price" 
                      placeholder="Price" 
                      value={product.price ? product.price : ''} 
                      onChange={this.handleInputChange}
                    />
                  </Col>
                  <Col>
                    <AvInput 
                      id="description" 
                      name="description" 
                      placeholder="Description" 
                      value={product.description ? product.description : ''} 
                      onChange={this.handleInputChange}
                    />
                  </Col>
                </AvGroup>
                <ReactQuill
                  placeholder='Long Description'
                  value={product.description_long ? product.description_long : ''}
                  onChange={this.handleQuillChange}
                />
                {/* <AvGroup>
                  <Label for="description_long">Long Description</Label>
                  <AvInput id="description_long" name="description_long" value={this.state.description_long} required onChange={this.handleInputChange} />
                  <AvFeedback>Long Description is required!</AvFeedback>
                </AvGroup> */}
              </ModalBody>
              <ModalFooter className="justify-content-between">
                <span className="btn btn-danger" onClick={this.toggle}>Cancel</span>
                <Button color="primary">Submit</Button>
              </ModalFooter>
            </AvForm>
          </Modal> : <Loader />
        }
      </React.Fragment>
    );
  }
}

export default ProductForm;
