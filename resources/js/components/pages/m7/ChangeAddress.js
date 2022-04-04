import React from "react";
import DatePicker from "react-datepicker";

import { CardHeader, CardBody, Button, Form, FormGroup, Label, Input } from "reactstrap";
import { M7Call } from '../../controllers/m7';

class ChangeAddress extends React.Component {
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
                    Country: 'Netherlands',
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
                Tenant: '',
                ContractNumber: '',
                ContractPeriod: '1',
                ContractStartDate: null,
                CustomerNumber: '',
                DealerNumber: '',
                OptedForNewsletter: 'false',
                WishDate: new Date(),
                SmartcardPackagesDTO: {
                    Decodernumber: '',
                    Smartcardnumber: ''
                },
                Productinfo: {
                    Campaigncode: '',
                    IsAddon: '',
                    Keywords: '',
                    ProductID: ''
                },
                MainSmartcard: '',
                TransactionType: ''
            }
        };

        this.handleSubmit = this.handleSubmit.bind(this);
    }

    handleChange(parent, e) {
        console.log(e.target)
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

        M7Call('ChangeAddress', this.state.data).then((res) => {
            let { data } = res;
            this.props.update(data);
        });
    }

    render() {
        return (
            <React.Fragment>
                <CardHeader>
                    <h4 className="mb-0">Change Address</h4>
                </CardHeader>
                <CardBody>
                    <Form className="form-m7" onSubmit={this.handleSubmit}>
                        <h4 className="mb-2">Billing Address</h4>
                        <FormGroup className="row">
                            <div className="col-12 col-md-3 mb-2">
                                <Label>House Number</Label>
                                <Input
                                    bsSize="lg"
                                    type="text"
                                    name="HouseNumber"
                                    value={this.state.data.Billingaddress.HouseNumber}
                                    onChange={(e) => this.handleChange('Billingaddress', e)}
                                />
                            </div>
                            <div className="col-12 col-md-3 mb-2">
                                <Label>House Number Extension</Label>
                                <Input
                                    bsSize="lg"
                                    type="text"
                                    name="HouseNumberExtension"
                                    value={this.state.data.Billingaddress.HouseNumberExtension}
                                    onChange={(e) => this.handleChange('Billingaddress', e)}
                                />
                            </div>
                            <div className="col-12 col-md-3 mb-2">
                                <Label>Street</Label>
                                <Input
                                    bsSize="lg"
                                    type="text"
                                    name="Street"
                                    value={this.state.data.Billingaddress.Street}
                                    onChange={(e) => this.handleChange('Billingaddress', e)}
                                />
                            </div>
                            <div className="col-12 col-md-3 mb-2">
                                <Label>City</Label>
                                <Input
                                    bsSize="lg"
                                    type="text"
                                    name="City"
                                    value={this.state.data.Billingaddress.City}
                                    onChange={(e) => this.handleChange('Billingaddress', e)}
                                />
                            </div>
                        </FormGroup>
                        <FormGroup className="row">
                            <div className="col-12 col-md-3 mb-2">
                                <Label>Municipality</Label>
                                <Input
                                    bsSize="lg"
                                    type="text"
                                    name="City"
                                    value={this.state.data.Billingaddress.Municipality}
                                    onChange={(e) => this.handleChange('Billingaddress', e)}
                                />
                            </div>
                            <div className="col-12 col-md-3 mb-2">
                                <Label>State</Label>
                                <Input
                                    bsSize="lg"
                                    type="text"
                                    name="State"
                                    value={this.state.data.Billingaddress.State}
                                    onChange={(e) => this.handleChange('Billingaddress', e)}
                                />
                            </div>
                            <div className="col-12 col-md-3 mb-2">
                                <Label>Country</Label>
                                <Input
                                    bsSize="lg"
                                    type="text"
                                    name="Country"
                                    value={this.state.data.Billingaddress.Country}
                                    disabled
                                />
                            </div>
                            <div className="col-12 col-md-3 mb-2">
                                <Label>Postal Code</Label>
                                <Input
                                    bsSize="lg"
                                    type="text"
                                    name="PostalCode"
                                    value={this.state.data.Billingaddress.PostalCode}
                                    onChange={(e) => this.handleChange('Billingaddress', e)}
                                />
                            </div>
                        </FormGroup>

                        <hr className="mt-4" />
                        <h4 className="mb-2">Billing Customer Details</h4>
                        <FormGroup className="row">
                            <div className="col-12 col-md-3 mb-2">
                                <Label>Email</Label>
                                <Input
                                    bsSize="lg"
                                    type="email"
                                    name="Email"
                                    value={this.state.data.BillingCustomerDetails.Email}
                                    onChange={(e) => this.handleChange('BillingCustomerDetails', e)}
                                />
                            </div>
                            <div className="col-12 col-md-3 mb-2">
                                <Label>Mobile</Label>
                                <Input
                                    bsSize="lg"
                                    type="text"
                                    name="Mobile"
                                    value={this.state.data.BillingCustomerDetails.Mobile}
                                    onChange={(e) => this.handleChange('BillingCustomerDetails', e)}
                                />
                            </div>
                            <div className="col-12 col-md-3 mb-2">
                                <Label>Phone</Label>
                                <Input
                                    bsSize="lg"
                                    type="text"
                                    name="Phone"
                                    value={this.state.data.BillingCustomerDetails.Phone}
                                    onChange={(e) => this.handleChange('BillingCustomerDetails', e)}
                                />
                            </div>
                        </FormGroup>

                        <hr className="mt-4" />
                        <FormGroup className="row">
                            <div className="col-12 col-md-3 mb-2">
                                <Label>Customer Number</Label>
                                <Input
                                    bsSize="lg"
                                    type="text"
                                    name="CustomerNumber"
                                    value={this.state.data.BillingCustomerDetails.CustomerNumber}
                                />
                            </div>
                            <div className="col-12 col-md-3 mb-2">
                                <Label>Contract Number</Label>
                                <Input
                                    bsSize="lg"
                                    type="text"
                                    name="ContractNumber"
                                    value={this.state.data.BillingCustomerDetails.ContractNumber}
                                    onChange={(e) => this.handleChange('BillingCustomerDetails', e)}
                                />
                            </div>
                            <div className="col-12 col-md-3 mb-2">
                                <Label>Contract Period</Label>
                                <Input
                                    bsSize="lg"
                                    type="number"
                                    name="ContractPeriod"
                                    value={this.state.data.BillingCustomerDetails.ContractPeriod}
                                    onChange={(e) => this.handleChange('BillingCustomerDetails', e)}
                                    min={1}
                                    max={12}
                                />
                            </div>
                            <div className="col-12 col-md-3 mb-2">
                                <Label>Wish Date</Label>
                                <DatePicker
                                    className="form-control form-control-lg"
                                    name="WishDate"
                                    dateFormat="dd/MM/yyyy"
                                    autoComplete="off"
                                    selected={this.state.data.BillingCustomerDetails.WishDate}
                                    onChange={(e) => this.handleDatePickerEvent('BillingCustomerDetails', 'WishDate', e)}
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

export default ChangeAddress;
