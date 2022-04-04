import React from "react";
import Select from "react-select";
import DatePicker from "react-datepicker";
import moment from 'moment';
import CurrencyInput from 'react-currency-input';

import { Button, Modal, ModalBody, ModalFooter, ModalHeader, Label, FormGroup, Row, Col, InputGroup, Input, InputGroupAddon } from "reactstrap";
import { AvForm } from "availity-reactstrap-validation";

import { GetDependenciesPlanLines, AddPlanLinePrices, UpdatePlanLinePrices } from '../../../../controllers/plans';

class PlanLinePricesForm extends React.Component {
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
      formName: 'Plan Line Price',
      id: null,
      plan_line_id: null,
      // parent_plan_line_id: null,
      fixed_price: null,
      margin: null,
      price_valid_from: null,
      parentPlanPrice: false
      // planLineOpts: []
    };

    this.toggle = this.toggle.bind(this);
    this.handleSubmit = this.handleSubmit.bind(this);
  }

  toggle() {
    this.props.hide()
  }

  update(val) {
    this.props.update(val)
  }

  handleInputChange(e) {
    e.preventDefault()

    const { name, value } = event.target
    
    this.setState({ [name]: value })
}

  handleSelectChange = (name, value) => {
    this.setState({ [name]: value });
  }

  handlePriceLineChange(name, typ, val) {
    this.setState({ [name]: val })
  }

  handleSubmit(e) {
    e.persist();

    const price = {
      plan_line_id: this.props.id2,
      // parent_plan_line_id: this.state.parent_plan_line_id.value,
      fixed_price: this.state.fixed_price,
      margin: this.state.margin && this.state.margin !== '' && this.state.margin !== 0 ? this.state.margin : 0,
      price_valid_from: moment(this.state.price_valid_from).format('DD-MM-YYYY')
    }

    if (this.props.selectedData) {
      price._method = 'PATCH';

      (async () => {
        await UpdatePlanLinePrices(price, this.props.selectedData.id)
          .then(res => {
            this.toggle()
            this.props.update(res.data.data, res.data.data.id);
          })
          .catch(err => {
            console.log(err)
          });
      })()
        .catch(err => {
          console.log(err)
        })
    } else {
      (async () => {
        await AddPlanLinePrices(price, this.props.id2)
          .then(res => {
            this.toggle()
            this.props.update();
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
    if (this.props.selectedData) {
        const { fixed_price, margin, price_valid_from } = this.props.selectedData

        this.setState({
            fixed_price,
            margin,
            price_valid_from: new Date(price_valid_from)
        })
    }
    
    (async () => {
      await GetDependenciesPlanLines()
        .then(res => {
          const datas = res.data,
                planLines = datas.plan_lines.data,
                planIndex = planLines.findIndex(plan => (parseInt(plan.id) === parseInt(this.props.id2))),
                plan = planLines[planIndex],
                parentPlanId = plan.parent_plan_line_id ? plan.parent_plan_line_id : null,
                parentPlanIndex = parentPlanId ? planLines.findIndex(plan => (parseInt(plan.id) === parseInt(parentPlanId))) : null,
                parentPlan = parentPlanIndex ? planLines[parentPlanIndex] : null,
                parentPlanPrice = parentPlan ? parentPlan.plan_line_price_fixed_price : null

          if (parentPlanPrice) {
            this.setState({ parentPlanPrice: true })
          }
          
          // let planLineOpt = [];

          // plan_lines.forEach((plan_line) => {
          //   if (plan_line.product && plan_line.product.product_type) {
          //     planLineOpt.push({
          //       label: plan_line.product.product_type.type,
          //       value: plan_line.id
          //     });
          //   }
          // });

          // this.setState({ planLineOpts: planLineOpt });

          // if (this.props.plan_line_price) {
          //   const price = this.props.plan_line_price,
          //     parent_plan_line_id = this.state.planLineOpts.filter(item => item.value === price.parent_plan_line_id);

          //   this.setState({
          //     id: price.id,
          //     parent_plan_line_id: parent_plan_line_id[0],
          //     fixed_price: price.fixed_price,
          //     margin: price.margin,
          //     price_valid_from: new Date(price.price_valid_from)
          //   })
          // }
        })
        .catch(err => {
          console.log(err)
        })
      })()
      .catch(err => {
        console.log(err)
      })
  }
  // componentDidMount() {
  //   const path = window.location.href.split('/'),
  //     id = path[path.length - 2];

  //   this.setState({ plan_line_id: id });

  //   (async () => {
  //     await GetDependenciesPlanLines()
  //       .then(res => {
  //         const datas = res.data,
  //           plan_lines = datas.plan_lines.data;

  //         let planLineOpt = [];

  //         plan_lines.forEach((plan_line) => {
  //           if (plan_line.product && plan_line.product.product_type) {
  //             planLineOpt.push({
  //               label: plan_line.product.product_type.type,
  //               value: plan_line.id
  //             });
  //           }
  //         });

  //         this.setState({ planLineOpts: planLineOpt });

  //         if (this.props.plan_line_price) {
  //           const price = this.props.plan_line_price,
  //             parent_plan_line_id = this.state.planLineOpts.filter(item => item.value === price.parent_plan_line_id);

  //           this.setState({
  //             id: price.id,
  //             parent_plan_line_id: parent_plan_line_id[0],
  //             fixed_price: price.fixed_price,
  //             margin: price.margin,
  //             price_valid_from: new Date(price.price_valid_from)
  //           })
  //         }
  //       })
  //       .catch(err => {
  //         console.log(err)
  //       });
  //   })()
  //     .catch(err => {
  //       console.log(err)
  //     })
  // }

  render() {
    return (
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
                <CurrencyInput
                  id="fixed_price"
                  className="form-control"
                  placeholder="Fixed price"
                  maxLength={ 8 }
                  value={this.state.fixed_price}
                  onChange={this.handlePriceLineChange.bind(this, 'fixed_price', 'price')}
                  disabled={(this.state.margin && parseFloat(this.state.margin) > 0) || this.state.parentPlanPrice ? true : false}
                />
              </Col>
              <Col>
              <InputGroup>
                  <Input
                      id="margin"
                      className="form-control"
                      type="number"
                      name="margin"
                      placeholder="Margin"
                      value={this.state.margin}
                      onChange={this.handleInputChange.bind(this)}
                      disabled={this.state.fixed_price && parseFloat(this.state.fixed_price) > 0 ? true : false}
                  />
                  <InputGroupAddon addonType="append">&#37;</InputGroupAddon>
              </InputGroup>
                {/* <CurrencyInput
                  id="margin" className="form-control"
                  placeholder="Margin"
                  value={this.state.margin}
                  onChange={this.handlePriceLineChange.bind(this, 'margin', 'price')}
                  // disabled={this.state.fixed_price !== null && this.state.fixed_price !== '0.00' ? true : false}
                  disabled={this.state.fixed_price && parseFloat(this.state.fixed_price) > 0 ? true : false}
                /> */}
              </Col>
            </FormGroup>
            
            <FormGroup>
              <DatePicker
                id="price_valid_from"
                className="form-control"
                name="price_valid_from"
                placeholderText="Price valid from"
                dateFormat="dd/MM/yyyy"
                autoComplete="off"
                selected={this.state.price_valid_from}
                onChange={this.handlePriceLineChange.bind(this, 'price_valid_from', 'date')}
              />
            </FormGroup>
          </ModalBody>
          <ModalFooter className="justify-content-between">
            <span className="btn btn-danger" onClick={this.toggle}>Cancel</span>
            <Button color="primary">Submit</Button>
          </ModalFooter>
        </AvForm>
      </Modal>
    );
  }
}

export default PlanLinePricesForm;
