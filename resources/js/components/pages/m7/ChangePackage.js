import React from "react";
import DatePicker from "react-datepicker";
import moment from "moment";

import { CardHeader, CardBody, Button, Form, FormGroup, Label, Input, CustomInput } from "reactstrap";
import { M7Call } from '../../controllers/m7';

class ChangePackage extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            data: {
                BankingInformation: {
                    AccountName: '',
                    BIC: '',
                    IBAN: ''
                },
                Billingaddress: {
                    HouseNumber: '',
                    HouseNumberExtension: '',
                    Street: '',
                    City: '',
                    Municipality: '',
                    State: '',
                    Country: '',
                    PostalCode: ''
                },
                BillingCustomerDetails: {
                    Title: '',
                    Firstname: '',
                    Middlename: '',
                    Surname: '',
                    Initials: '',
                    Gender: '',
                    DateOfBirth: null,
                    Email: '',
                    Mobile: '',
                    Phone: ''
                },
                ContractNumber: '',
                ContractPeriod: '1',
                ContractStartDate: moment.now(),
                CustomerNumber: '',
                OptedForNewsletter: 'false',
                WishDate: moment.now(),
                SmartcardPackagesDTO: {
                    Decodernumber: '',
                    MainSmartcard: '',
                    Smartcardnumber: ''
                },
                Productinfo: {
                    Campaigncode: '',
                    IsAddon: 'false',
                    Keywords: '',
                    ProductID: ''
                },
                TransactionType: ''
            }
        };

        this.handleSubmit = this.handleSubmit.bind(this);
    }

    handleChange(parent, e) {
        if (parent) {
            this.setState({
                data: {
                    ...this.state.data,
                    [parent]: {
                        ...this.state.data[parent],
                        [e.target.name]: e.target.value
                    }
                }
            });
        } else {
            this.setState({
                data: {
                    ...this.state.data,
                    [e.target.name]: e.target.value
                }
            });
        }
    }

    handleDatePickerEvent(parent, name, date) {
        if (parent) {
            this.setState({
                data: {
                    ...this.state.data,
                    [parent]: {
                        ...this.state.data[parent],
                        [name]: date
                    }
                }
            });
        } else {
            this.setState({
                data: {
                    ...this.state.data,
                    [name]: date
                }
            });
        }
    }

    async handleSubmit(e) {
        e.preventDefault();
        this.props.loading();

        M7Call('ChangePackage', this.state.data).then((res) => {
            let { data } = res;
            this.props.update(data);
        });
    }

    render() {
        return (
            <React.Fragment>
                <CardHeader>
                    <h4 className="mb-0">Change Package</h4>
                </CardHeader>
                <CardBody>
                    <Form className="form-m7" onSubmit={this.handleSubmit}>
                        <h4 className="mb-2">Billing Customer Details</h4>
                        <FormGroup className="row">
                            <div className="col-12 col-md-3 mb-2">
                                <Label>Contract Number</Label>
                                <Input
                                    bsSize="lg"
                                    type="text"
                                    name="ContractNumber"
                                    value={this.state.data.ContractNumber}
                                    onChange={(e) => this.handleChange(null, e)}
                                />
                            </div>
                            <div className="col-12 col-md-3 mb-2">
                                <Label>Contract Period</Label>
                                <Input
                                    bsSize="lg"
                                    type="number"
                                    name="ContractPeriod"
                                    value={this.state.data.ContractPeriod}
                                    onChange={(e) => this.handleChange(null, e)}
                                    min={1}
                                    max={12}
                                />
                            </div>
                            <div className="col-12 col-md-3 mb-2">
                                <Label>Customer Number</Label>
                                <Input
                                    bsSize="lg"
                                    type="text"
                                    name="CustomerNumber"
                                    value={this.state.data.CustomerNumber}
                                    onChange={(e) => this.handleChange(null, e)}
                                />
                            </div>
                            <div className="col-12 col-md-3 mb-2">
                                <Label>Contract Start Date</Label>
                                <DatePicker
                                    className="form-control form-control-lg"
                                    name="ContractStartDate"
                                    dateFormat="dd/MM/yyyy"
                                    autoComplete="off"
                                    selected={this.state.data.ContractStartDate}
                                    onChange={(e) => this.handleDatePickerEvent(null, 'ContractStartDate', e)}
                                    showMonthDropdown
                                    showYearDropdown
                                />
                            </div>
                            <div className="col-12 col-md-3 mb-2">
                                <Label>Wish Date</Label>
                                <DatePicker
                                    className="form-control form-control-lg"
                                    name="WishDate"
                                    dateFormat="dd/MM/yyyy"
                                    autoComplete="off"
                                    selected={this.state.data.WishDate}
                                    onChange={(e) => this.handleDatePickerEvent(null, 'WishDate', e)}
                                    disabled
                                />
                            </div>
                        </FormGroup>

                        <hr className="mt-4" />
                        <h4 className="mb-2">Smartcard Packages DTO</h4>
                        <FormGroup className="row">
                            <div className="col-12 col-md-3 mb-2">
                                <Label>Smartcard number</Label>
                                <Input
                                    bsSize="lg"
                                    type="text"
                                    name="Smartcardnumber"
                                    value={this.state.data.SmartcardPackagesDTO.Smartcardnumber}
                                    onChange={(e) => this.handleChange('SmartcardPackagesDTO', e)}
                                />
                            </div>
                        </FormGroup>

                        <hr className="mt-4" />
                        <h4 className="mb-2">Product Info</h4>
                        <FormGroup className="row">
                            <div className="col-12 col-md-3 mb-2">
                                <Label>Product ID</Label>
                                <Input
                                    bsSize="lg"
                                    type="text"
                                    name="ProductID"
                                    value={this.state.data.Productinfo.ProductID}
                                    onChange={(e) => this.handleChange('Productinfo', e)}
                                />
                            </div>
                            <div className="col-12 col-md-3 mb-2">
                                <Label>Is Add-on</Label>
                                <CustomInput
                                    bsSize="lg"
                                    id="IsAddonTrue3"
                                    type="radio"
                                    name="IsAddon"
                                    label="True"
                                    value="true"
                                    checked={this.state.data.Productinfo.IsAddon === 'true'}
                                    onChange={(e) => this.handleChange('Productinfo', e)}
                                />
                                <CustomInput
                                    bsSize="lg"
                                    id="IsAddonFalse3"
                                    type="radio"
                                    name="IsAddon"
                                    label="False"
                                    value="false"
                                    checked={this.state.data.Productinfo.IsAddon === 'false'}
                                    onChange={(e) => this.handleChange('Productinfo', e)}
                                />
                            </div>
                        </FormGroup>

                        <FormGroup>
                            <Button color="primary">Submit</Button>
                        </FormGroup>
                    </Form>
                </CardBody>
            </React.Fragment>
        );
    }
}

export default ChangePackage;
