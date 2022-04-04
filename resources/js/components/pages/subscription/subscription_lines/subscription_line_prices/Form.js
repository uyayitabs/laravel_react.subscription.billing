import React from "react";
import Select from "react-select";
import DatePicker from "react-datepicker";
import moment from 'moment';
import CurrencyInput from 'react-currency-input';

import { Button, Modal, ModalBody, ModalFooter, ModalHeader, Input, InputGroup, InputGroupAddon, FormGroup, Row, Col } from "reactstrap";
import { AvForm } from "availity-reactstrap-validation";

import { GetDependenciesSubscriptionLines, AddSubscriptionLinePrice, UpdateSubscriptionLinePrice } from '../../../../controllers/subscriptions';

class SubscriptionLinePricesForm extends React.Component {
    constructor(props) {
        super(props);

        let type;

        if (this.props.data) {
            type = 'Edit';
        } else {
            type = 'Add';
        }

        this.state = {
            formType: type,
            formName: 'Subscription Line Price',
            id: null,
            // subscription_line_id: null,
            // parent_plan_line_id: null,
            fixed_price: null,
            margin: null,
            price_valid_from: null,
            // subscriptionLineOpts: []
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
        this.setState({ [name]: value })
    }

    handlePriceLineChange(name, typ, val) {
        this.setState({ [name]: val })
    }

    handleSubmit(e) {
        e.persist();

        const price = {
            // parent_plan_line_id: this.state.parent_plan_line_id.value,
            fixed_price: this.state.fixed_price && this.state.fixed_price !== '' && this.state.fixed_price !== '0.00' ? this.state.fixed_price : 0.00,
            margin: this.state.margin && this.state.margin !== '' && this.state.margin !== 0 ? this.state.margin : 0,
            price_valid_from: moment(this.state.price_valid_from).format('DD-MM-YYYY')
        }

        if (this.props.selectedData) {
            price._method = 'PATCH';

            (async () => {
                await UpdateSubscriptionLinePrice(price, this.props.selectedData.id)
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
                await AddSubscriptionLinePrice(price, this.props.id2)
                    .then(res => {
                        this.toggle()
                        this.props.update();
                    })
                    .catch(err => {
                        console.log(err)
                    });
            })().catch(err => {
                console.log(err)
            })
        }
    }

    componentDidMount() {
        if (this.props.selectedData) {
            const { fixed_price, margin, price_valid_from } = this.props.selectedData,
                  date = price_valid_from.split('-')

            this.setState({
                fixed_price,
                margin,
                price_valid_from: new Date(`${ date[2] }-${ date[1] }-${ date[0] }`)
            })
        }
        // const path = window.location.href.split('/'),
        //     id = path[path.length - 2];

        // this.setState({ subscription_line_id: id });
    }

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
                        <Row>
                            <Col md={12}>
                                <FormGroup className="row">
                                    <Col>
                                        <CurrencyInput
                                            id="fixed_price"
                                            className="form-control"
                                            placeholder="Fixed Price"
                                            maxLength={ 8 }
                                            value={this.state.fixed_price}
                                            onChange={this.handlePriceLineChange.bind(this, 'fixed_price', 'price')}
                                            disabled={this.state.margin && parseFloat(this.state.margin) > 0 ? true : false}
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
                                    </Col>
                                </FormGroup>
                                <FormGroup className="row">
                                    <Col>
                                        <DatePicker
                                            id="price_valid_from"
                                            className="form-control"
                                            name="price_valid_from"
                                            dateFormat="dd/MM/yyyy"
                                            autoComplete="off"
                                            placeholderText="Price Valid From"
                                            selected={this.state.price_valid_from}
                                            onChange={this.handlePriceLineChange.bind(this, 'price_valid_from', 'date')}
                                        />
                                    </Col>
                                </FormGroup>
                            </Col>
                        </Row>
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

export default SubscriptionLinePricesForm;
