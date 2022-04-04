import React from "react";

import { CardHeader, CardBody, Button, Form, FormGroup, Label, Input } from "reactstrap";
import { M7Call } from '../../controllers/m7';

class ResetPin extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            data: {
                ResetPin: {
                    CustomerNumber: '',
                    ContractNumber: '',
                    Decodernumber: '',
                    Smartcardnumber: '',
                    OldDecodernumber: '',
                    OldSmartcardnumber: ''
                },
                WishDate: '',
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

    async handleSubmit(e) {
        e.preventDefault();
        this.props.loading();

        M7Call('ResetPin', this.state.data).then((res) => {
            let { data } = res;
            this.props.update(data);
        });
    }

    render() {
        return (
            <React.Fragment>
                <CardHeader>
                    <h4 className="mb-0">Reset Pin</h4>
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
                                    value={this.state.data.ResetPin.CustomerNumber}
                                    onChange={(e) => this.handleChange('ResetPin', e)}
                                />
                            </div>
                            <div className="col-12 col-md-3 mb-2">
                                <Label>Smartcard number</Label>
                                <Input
                                    bsSize="lg"
                                    type="text"
                                    name="Smartcardnumber"
                                    value={this.state.data.ResetPin.Smartcardnumber}
                                    onChange={(e) => this.handleChange('ResetPin', e)}
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

export default ResetPin;
