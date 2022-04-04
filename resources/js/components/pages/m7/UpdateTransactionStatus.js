import React from "react";

import { CardHeader, CardBody, Button, Form, FormGroup, Label, Input } from "reactstrap";
import { M7Call } from '../../controllers/m7';

class UpdateTransactionStatus extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            data: {
                CustomerNumber: '',
                ReferenceNumber: '',
                Result: '',
                ResultDescription: '',
                Soapmethod: '',
                TransactionID: '',
                XMLInfo: ''
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

    async handleSubmit(e) {
        e.preventDefault();
        this.props.loading();

        M7Call('UpdateTransactionStatus', this.state.data).then((res) => {
            let { data } = res;
            this.props.update(data);
        });
    }

    render() {
        return (
            <React.Fragment>
                <CardHeader>
                    <h4 className="mb-0">Update Transaction Status</h4>
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
                                    value={this.state.data.CustomerNumber}
                                    onChange={(e) => this.handleChange(null, e)}
                                />
                            </div>
                            <div className="col-12 col-md-3 mb-2">
                                <Label>Reference Number</Label>
                                <Input
                                    bsSize="lg"
                                    type="text"
                                    name="ReferenceNumber"
                                    value={this.state.data.ReferenceNumber}
                                    onChange={(e) => this.handleChange(null, e)}
                                />
                            </div>
                            <div className="col-12 col-md-3 mb-2">
                                <Label>Result</Label>
                                <Input
                                    bsSize="lg"
                                    type="text"
                                    name="Result"
                                    value={this.state.data.Result}
                                    onChange={(e) => this.handleChange(null, e)}
                                />
                            </div>
                            <div className="col-12 col-md-3 mb-2">
                                <Label>Result Description</Label>
                                <Input
                                    bsSize="lg"
                                    type="text"
                                    name="ResultDescription"
                                    value={this.state.data.ResultDescription}
                                    onChange={(e) => this.handleChange(null, e)}
                                />
                            </div>
                            <div className="col-12 col-md-3 mb-2">
                                <Label>Soap method</Label>
                                <Input
                                    bsSize="lg"
                                    type="text"
                                    name="Soapmethod"
                                    value={this.state.data.Soapmethod}
                                    onChange={(e) => this.handleChange(null, e)}
                                />
                            </div>
                            <div className="col-12 col-md-3 mb-2">
                                <Label>XML Info</Label>
                                <Input
                                    bsSize="lg"
                                    type="text"
                                    name="XMLInfo"
                                    value={this.state.data.XMLInfo}
                                    onChange={(e) => this.handleChange(null, e)}
                                />
                            </div>
                            <div className="col-12 col-md-3 mb-2">
                                <Label>Transaction ID</Label>
                                <Input
                                    bsSize="lg"
                                    type="text"
                                    name="TransactionID"
                                    value={this.state.data.TransactionID}
                                    onChange={(e) => this.handleChange(null, e)}
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

export default UpdateTransactionStatus;
