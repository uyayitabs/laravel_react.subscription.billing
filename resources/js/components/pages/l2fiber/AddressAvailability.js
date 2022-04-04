import React from "react";

import { CardHeader, CardTitle, CardBody, Button, Form, FormGroup, Label, Input, Table } from "reactstrap";
import Loader from '../../components/Loader';

import { GetAddressAvailability } from '../../controllers/l2fiber';

class AddressAvailability extends React.Component {
  constructor(props) {
    super(props);

    this.state = {
      postalCode: '',
      streetNr: '',
      streetNrAddition: '',
      room: '',
      result: {
        status: '',
        alternatives: ''
      },
      loading: false
    };

    this.handleChange = this.handleChange.bind(this);
    this.handleSubmit = this.handleSubmit.bind(this);
  }

  handleChange(e) {
    this.setState({ [e.target.name]: e.target.value });
  }

  handleSubmit(e) {
    e.preventDefault();

    let self = this;
    self.setState({
      loading: true
    });

    (async () => {
      let params = {
        postalCode: this.state.postalCode,
        streetNr: this.state.streetNr,
        streetNrAddition: this.state.streetNrAddition,
        room: this.state.room,
      }
      GetAddressAvailability(params)
        .then(res => {
          var resultData = { ...this.state.result }
          resultData.status = res.data.status;
          resultData.alternatives = res.data.alternatives != null ? res.data.alternatives.join(", ") : "";
          self.setState({
            result: resultData,
            loading: false
          });
        });

    })()
      .catch(err => {
        console.log(err)
      })

  }

  render() {

    const AvailabilityResult = (props) => {
      return (
        <React.Fragment>
          {!this.state.loading ?
            <React.Fragment>
              <CardHeader>
                  <h4 className="mb-0">Availability</h4>
              </CardHeader>
              <CardBody>
                <Table className="mb-0">
                    <tbody>
                      <tr>
                        <td>Status: </td>
                        <td>{props.result.status}</td>
                      </tr>
                      <tr>
                        <td>Alternatives: </td>
                        <td>{props.result.alternatives}</td>
                      </tr>
                    </tbody>
                  </Table>          
              </CardBody>
            </React.Fragment>
            : <Loader />}
        </React.Fragment>
      )
    }

    return (
      <React.Fragment>
          <CardHeader>
              <h4 className="mb-0">Address Availability</h4>
          </CardHeader>
          <CardBody>
            <Form onSubmit={this.handleSubmit}>

              <FormGroup>
                <Label>Postal Code</Label>
                <Input
                  bsSize="lg"
                  type="text"
                  name="postalCode"
                  value={this.state.postalCode}
                  onChange={this.handleChange}
                />
              </FormGroup>
              <FormGroup>
                <Label>Street No.</Label>
                <Input
                  bsSize="lg"
                  type="text"
                  name="streetNr"
                  value={this.state.streetNr}
                  onChange={this.handleChange}
                />
              </FormGroup>
              <FormGroup>
                <Label>Street No. Addition</Label>
                <Input
                  bsSize="lg"
                  type="text"
                  name="streetNrAddition"
                  value={this.state.streetNrAddition}
                  onChange={this.handleChange}
                />
              </FormGroup>
              <FormGroup>
                <Label>Room</Label>
                <Input
                  bsSize="lg"
                  type="text"
                  name="room"
                  value={this.state.room}
                  onChange={this.handleChange}
                />
              </FormGroup>
              <FormGroup>
                <Button color="primary">Submit</Button>
              </FormGroup>
            </Form>
          </CardBody>        
          <AvailabilityResult result={this.state.result} />
      </React.Fragment>
    )
  }
}

export default AddressAvailability;
