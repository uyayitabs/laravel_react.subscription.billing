import React from "react";
import DatePicker from "react-datepicker";

import { CardHeader, CardBody, Button, Form, FormGroup, Label, Input } from "reactstrap";
import { M7Call } from '../../controllers/m7';

class Reconnect extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            data: {
                Reconnect: {
                    CustomerNumber: '',
                    ContractNumber: '',
                    Decodernumber: '',
                    Smartcardnumber: '',
                    OldDecodernumber: '',
                    OldSmartcardnumber: '',
                },
                WishDate: new Date(),
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

        M7Call('Reconnect', this.state.data).then((res) => {
            let { data } = res;
            this.props.update(data);
        });
    }

    render() {
        return (
            <React.Fragment>
                <CardHeader>
                    <h4 className="mb-0">Reconnect</h4>
                </CardHeader>
                <CardBody>
                    <Form className="form-m7" onSubmit={this.handleSubmit}>
                        <FormGroup className="row">
                            <div className="col-12 col-md-3 mb-2">
                                <Label>Customer Number</Label>
                                <Input
                                    bsSize="lg"
                                    type="text"
                                    name="CustomerNumber"
                                    value={this.state.data.Reconnect.CustomerNumber}
                                    onChange={(e) => this.handleChange('Reconnect', e)}
                                />
                            </div>
                            <div className="col-12 col-md-3 mb-2">
                                <Label>Contract Number</Label>
                                <Input
                                    bsSize="lg"
                                    type="text"
                                    name="ContractNumber"
                                    value={this.state.data.Reconnect.ContractNumber}
                                    onChange={(e) => this.handleChange('Reconnect', e)}
                                />
                            </div>
                            <div className="col-12 col-md-3 mb-2">
                                <Label>Smartcard number</Label>
                                <Input
                                    bsSize="lg"
                                    type="text"
                                    name="Smartcardnumber"
                                    value={this.state.data.Reconnect.Smartcardnumber}
                                    onChange={(e) => this.handleChange('Reconnect', e)}
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
                                    onChange={(e) => this.handleDatePickerEvent('Reconnect', 'WishDate', e)}
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

export default Reconnect;
