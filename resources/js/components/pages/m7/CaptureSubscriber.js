import React from "react";
import DatePicker from "react-datepicker";
import Select from "react-select";

import { CardHeader, CardBody, Button, Form, FormGroup, Label, Input, CustomInput } from "reactstrap";
import { M7Call } from '../../controllers/m7';

const genderOpts = [
    {
        value: 'V',
        label: 'Vrouw'
    }, {
        value: 'M',
        label: 'Man'
    }
]

class CaptureSubscriber extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            Gender: {
                value: 'V',
                label: 'Vrouw'
            },
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
                    Gender: 'V',
                    DateOfBirth: null,
                    Email: '',
                    Mobile: '',
                    Phone: ''
                },
                ContractNumber: '',
                ContractPeriod: '1',
                ContractStartDate: null,
                CustomerNumber: '',
                DealerNumber: '',
                OptedForNewsletter: 'false',
                WishDate: new Date(),
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
        this.testCaptureSubscriber = this.testCaptureSubscriber.bind(this);
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

    handleSelectChange = (parent, name, value) => {
        this.setState({
            data: {
                ...this.state.data,
                [parent]: {
                    ...this.state.data[parent],
                    [name]: value.value
                }
            }
        });
        if (name == 'Gender') this.setState({ 'Gender': value });
    }

    async handleSubmit(e) {
        e.preventDefault();
        this.props.loading();

        M7Call('CaptureSubscriber', this.state.data).then((res) => {
            let { data } = res;
            this.props.update(data);
        });
    }

    async testCaptureSubscriber(e) {
        const { data } = await M7Call('CaptureSubscriber/Test', this.state.data);
        this.props.update(data);
    }

    render() {
        return (
            <React.Fragment>
                <CardHeader>
                    <h4 className="mb-0">Capture Subscriber</h4>
                </CardHeader>
                <CardBody>
                    <Form className="form-m7" onSubmit={this.handleSubmit}>
                        <h4 className="mb-2">Banking Information</h4>
                        <FormGroup className="row">
                            <div className="col-12 col-md-3 mb-2">
                                <Label>Account Name</Label>
                                <Input
                                    bsSize="lg"
                                    type="text"
                                    name="AccountName"
                                    value={this.state.data.BankingInformation.AccountName}
                                    onChange={(e) => this.handleChange('BankingInformation', e)}
                                />
                            </div>
                            <div className="col-12 col-md-3 mb-2">
                                <Label>BIC</Label>
                                <Input
                                    bsSize="lg"
                                    type="text"
                                    name="BIC"
                                    value={this.state.data.BankingInformation.BIC}
                                    onChange={(e) => this.handleChange('BankingInformation', e)}
                                />
                            </div>
                            <div className="col-12 col-md-3 mb-2">
                                <Label>IBAN</Label>
                                <Input
                                    bsSize="lg"
                                    type="text"
                                    name="IBAN"
                                    value={this.state.data.BankingInformation.IBAN}
                                    onChange={(e) => this.handleChange('BankingInformation', e)}
                                />
                            </div>
                        </FormGroup>

                        <hr className="mt-4" />
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
                                    name="Municipality"
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
                                <Label>Title</Label>
                                <Input
                                    bsSize="lg"
                                    type="text"
                                    name="Title"
                                    value={this.state.data.BillingCustomerDetails.Title}
                                    onChange={(e) => this.handleChange('BillingCustomerDetails', e)}
                                />
                            </div>
                            <div className="col-12 col-md-3 mb-2">
                                <Label>First name</Label>
                                <Input
                                    bsSize="lg"
                                    type="text"
                                    name="Firstname"
                                    value={this.state.data.BillingCustomerDetails.Firstname}
                                    onChange={(e) => this.handleChange('BillingCustomerDetails', e)}
                                />
                            </div>
                            <div className="col-12 col-md-3 mb-2">
                                <Label>Middle name</Label>
                                <Input
                                    bsSize="lg"
                                    type="text"
                                    name="Middlename"
                                    value={this.state.data.BillingCustomerDetails.Middlename}
                                    onChange={(e) => this.handleChange('BillingCustomerDetails', e)}
                                />
                            </div>
                            <div className="col-12 col-md-3 mb-2">
                                <Label>Surname</Label>
                                <Input
                                    bsSize="lg"
                                    type="text"
                                    name="Surname"
                                    value={this.state.data.BillingCustomerDetails.Surname}
                                    onChange={(e) => this.handleChange('BillingCustomerDetails', e)}
                                />
                            </div>
                        </FormGroup>
                        <FormGroup className="row">
                            <div className="col-12 col-md-3 mb-2">
                                <Label>Initials</Label>
                                <Input
                                    bsSize="lg"
                                    type="text"
                                    name="Initials"
                                    value={this.state.data.BillingCustomerDetails.Initials}
                                    onChange={(e) => this.handleChange('BillingCustomerDetails', e)}
                                />
                            </div>
                            <div className="col-12 col-md-3 mb-2">
                                <Label>Gender</Label>
                                <Select
                                    className="react-select-container"
                                    classNamePrefix="react-select"
                                    options={genderOpts}
                                    value={this.state.Gender}
                                    onChange={(e) => this.handleSelectChange('BillingCustomerDetails', 'Gender', e)}
                                    maxMenuHeight="100"
                                />
                            </div>
                            <div className="col-12 col-md-3 mb-2">
                                <Label>Date of Birth</Label>
                                <DatePicker
                                    className="form-control form-control-lg"
                                    name="DateOfBirth"
                                    dateFormat="dd/MM/yyyy"
                                    autoComplete="off"
                                    selected={this.state.data.BillingCustomerDetails.DateOfBirth}
                                    onChange={(e) => this.handleDatePickerEvent('BillingCustomerDetails', 'DateOfBirth', e)}
                                    showMonthDropdown
                                    showYearDropdown
                                />
                            </div>
                        </FormGroup>
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
                        </FormGroup>
                        <FormGroup className="row">
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
                            {/* <div className="col-12 col-md-3 mb-2">
                                <Label>Opted for Newsletter</Label>
                                <CustomInput
                                    bsSize="lg"
                                    id="OptedForNewsletterTrue"
                                    type="radio"
                                    name="OptedForNewsletter"
                                    label="True"
                                    value="true"
                                    checked={this.state.data.OptedForNewsletter === 'true'}
                                    onChange={(e) => this.handleChange(null, e)}
                                    disabled
                                />
                                <CustomInput
                                    bsSize="lg"
                                    id="OptedForNewsletterFalse"
                                    type="radio"
                                    name="OptedForNewsletter"
                                    label="False"
                                    value="false"
                                    checked={this.state.data.OptedForNewsletter === 'false'}
                                    onChange={(e) => this.handleChange(null, e)}
                                    disabled
                                />
                            </div> */}
                        </FormGroup>

                        <hr className="mt-4" />
                        <h4 className="mb-2">Smartcard Packages DTO</h4>
                        <FormGroup className="row">
                            <div className="col-12 col-md-3 mb-2">
                                <Label>Decoder number</Label>
                                <Input
                                    bsSize="lg"
                                    type="number"
                                    name="Decodernumber"
                                    value={this.state.data.SmartcardPackagesDTO.Decodernumber}
                                    onChange={(e) => this.handleChange('SmartcardPackagesDTO', e)}
                                />
                            </div>
                            <div className="col-12 col-md-3 mb-2">
                                <Label>Main Smartcard</Label>
                                <Input
                                    bsSize="lg"
                                    type="text"
                                    name="MainSmartcard"
                                    value={this.state.data.SmartcardPackagesDTO.MainSmartcard}
                                    onChange={(e) => this.handleChange('SmartcardPackagesDTO', e)}
                                />
                            </div>
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
                            {/* <div className="col-12 col-md-3 mb-2">
                                <Label>Campaign Code</Label>
                                <Input
                                    bsSize="lg"
                                    type="text"
                                    name="Campaigncode"
                                    value={this.state.data.Productinfo.Campaigncode}
                                    onChange={(e) => this.handleChange('Productinfo', e)}
                                    disabled
                                />
                            </div> */}
                            {/* <div className="col-12 col-md-3 mb-2">
                                <Label>Keywords</Label>
                                <Input
                                    bsSize="lg"
                                    type="text"
                                    name="Keywords"
                                    value={this.state.data.Productinfo.Keywords}
                                    onChange={(e) => this.handleChange('Productinfo', e)}
                                    disabled
                                />
                            </div> */}
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
                                    id="IsAddonTrue"
                                    type="radio"
                                    name="IsAddon"
                                    label="True"
                                    value="true"
                                    checked={this.state.data.Productinfo.IsAddon === 'true'}
                                    onChange={(e) => this.handleChange('Productinfo', e)}
                                />
                                <CustomInput
                                    bsSize="lg"
                                    id="IsAddonFalse"
                                    type="radio"
                                    name="IsAddon"
                                    label="False"
                                    value="false"
                                    checked={this.state.data.Productinfo.IsAddon === 'false'}
                                    onChange={(e) => this.handleChange('Productinfo', e)}
                                />
                            </div>
                        </FormGroup>

                        {/* <hr className="mt-4" />
                        <FormGroup className="row">
                            <div className="col-12 col-md-3 mb-2">
                                <Label>Transaction Type</Label>
                                <Input
                                    bsSize="lg"
                                    type="text"
                                    name="TransactionType"
                                    value={this.state.data.TransactionType}
                                    onChange={(e) => this.handleChange(null, e)}
                                    disabled
                                />
                            </div>
                        </FormGroup> */}

                        <FormGroup>
                            <Button color="primary">Submit</Button>
                        </FormGroup>
                    </Form>
                </CardBody>
            </React.Fragment>
        );
    }
}

export default CaptureSubscriber;
