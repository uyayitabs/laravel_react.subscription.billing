import React from "react";
import DatePicker from "react-datepicker";
import moment from 'moment';

import classnames from "classnames";
import { Button, Form, FormGroup, Label, Input, Container, Card, CardBody, CardHeader, Nav, NavItem, NavLink, TabContent, TabPane } from "reactstrap";
import Select from "react-select";

import { AddressTermination } from '../../controllers/l2fiber';

class TerminateConnection extends React.Component {
  constructor(props) {
    super(props);

    this.state = {
      activeTab: '1',
      domain: 'domain-here',

      swap : { 
        addressPublicId: '',
        newOntSerial: '',
        newOntDeviceType: '',
        oldOntSerial: '',
        oldOntDeviceType: '',
        action: 0
      },
      return : { 
        addressPublicId: '',
        ontSerial: '',
        ontDeviceType: '',
        action: 1
      },
      returnDefect : { 
        addressPublicId: '',
        ontSerial: '',
        ontDeviceType: '',
        action: 2
      },
    };

    
    this.handleSwapChange = this.handleSwapChange.bind(this);
    this.handleSwapSubmit = this.handleSwapSubmit.bind(this);

    this.handleReturnChange = this.handleReturnChange.bind(this);
    this.handleReturnSubmit = this.handleReturnSubmit.bind(this);

    this.handleReturnDefectChange = this.handleReturnDefectChange.bind(this);
    this.handleReturnDefectSubmit = this.handleReturnDefectSubmit.bind(this);

    this.toggle = this.toggle.bind(this);
  }

  toggle(tab) {
    if (this.state.activeTab !== tab) {
        this.setState({ activeTab: tab });
    }
}
  
  handleSwapChange(e) { 
    this.setState({
      swap: {
        ...this.state.swap,
        [e.target.name]: e.target.value
      }
    })
  }

  handleReturnChange(e) { 
    this.setState({
      return: {
        ...this.state.return,
        [e.target.name]: e.target.value
      }
    })
  }

  handleReturnDefectChange(e) { 
    this.setState({
      returnDefect: {
        ...this.state.returnDefect,
        [e.target.name]: e.target.value
      }
    })
  }

  handleSelectChange = (name, value) => {
    this.setState({
      returnDefect: {
        ...this.state.returnDefect,
        [name]: value
      }
    });
  }

  handleDatePickerChange(name, date) {
    this.setState({ [name]: date });
  }

  handleSwapSubmit(e) {
    e.preventDefault();
    console.error("#swap", this.state.swap);

    (async () => {
      await AddressTermination(this.state.swap)
          .then(res => {
             console.error(JSON.stringify(res.data.data));
          })
          .catch(err => {
              console.log(err);
          });
      })().catch(err => {
          console.error(err);
      })
  }

  handleReturnSubmit(e) {
    e.preventDefault();

    console.error("#return", this.state.return);
    (async () => {
      await AddressTermination(this.state.return)
          .then(res => {
             console.error(JSON.stringify(res.data.data));
          })
          .catch(err => {
              console.log(err);
          });
      })().catch(err => {
          console.error(err);
      })
  }

  handleReturnDefectSubmit(e) {
    e.preventDefault();
    console.error("#returnDefect", this.state.returnDefect);
    (async () => {
      await AddressTermination(this.state.returnDefect)
          .then(res => {
             console.error(JSON.stringify(res.data.data));
          })
          .catch(err => {
              console.log(err);
          });
      })().catch(err => {
          console.error(err);
      })
  }

  render() {
    return (
        <React.Fragment>
          <CardHeader>
              <h4 className="mb-0">Terminate Connection</h4>
          </CardHeader>
          <CardBody>        
            <Card className="tabs-service">
              <CardHeader>
                  <Nav tabs>
                      <NavItem>
                          <NavLink
                              className={classnames({ active: this.state.activeTab === "1" })}
                              onClick={() => {
                                  this.toggle("1");
                              }}
                          >
                              <h5>Swap</h5>
                          </NavLink>
                      </NavItem>
                      <NavItem>
                          <NavLink
                              className={classnames({ active: this.state.activeTab === "2" })}
                              onClick={() => {
                                  this.toggle("2");
                              }}>
                              <h5>Return</h5>
                          </NavLink>
                      </NavItem>
                      <NavItem>
                          <NavLink
                              className={classnames({ active: this.state.activeTab === "3" })}
                              onClick={() => {
                                  this.toggle("3");
                              }}>
                              <h5>Return Defect</h5>
                          </NavLink>
                      </NavItem>
                  </Nav>
              </CardHeader>

              <CardBody>
                  <TabContent activeTab={this.state.activeTab}>
                    {/* SWAP */}
                    <TabPane tabId="1">
                      <Container fluid className="p-0 mt-4">
                        <Form onSubmit={ this.handleSwapSubmit } className="mb-1">
                          <FormGroup>
                            <Label>Address Public Id</Label>
                            <Input
                              bsSize="lg"
                              type="text"
                              name="addressPublicId"
                              value={ this.state.swap.addressPublicId }
                              onChange={ this.handleSwapChange }
                              required
                            />
                          </FormGroup>
                          <FormGroup>
                            <Label>*NEW Ont Serial</Label>
                            <Input
                              bsSize="lg"
                              type="text"
                              name="newOntSerial"
                              value={ this.state.swap.newOntSerial }
                              onChange={ this.handleSwapChange }
                              required
                            />
                          </FormGroup>
                          <FormGroup>
                            <Label>*NEW Ont DeviceType</Label>
                            <Input
                              bsSize="lg"
                              type="text"
                              name="newOntDeviceType"
                              value={ this.state.swap.newOntDeviceType }
                              onChange={ this.handleSwapChange }
                              required
                            />
                          </FormGroup>
                          <FormGroup>
                            <Label>*OLD Ont Serial</Label>
                            <Input
                              bsSize="lg"
                              type="text"
                              name="oldOntSerial"
                              value={ this.state.swap.oldOntSerial }
                              onChange={ this.handleSwapChange }
                              required
                            />
                          </FormGroup>
                          <FormGroup>
                            <Label>*OLD Ont DeviceType</Label>
                            <Input
                              bsSize="lg"
                              type="text"
                              name="oldOntDeviceType"
                              value={ this.state.swap.oldOntDeviceType }
                              onChange={ this.handleSwapChange }
                              required
                            />
                          </FormGroup>

                          <FormGroup>
                            <Button color="primary">Submit</Button>
                          </FormGroup>
                        </Form>
                      </Container>
                    </TabPane>

                    {/* RETURN */}
                    <TabPane tabId="2">
                      <Container fluid className="p-0 mt-4">
                        <Form onSubmit={ this.handleReturnSubmit } className="mt-4">
                          <FormGroup>
                            <Label>Address Public Id</Label>
                            <Input
                              bsSize="lg"
                              type="text"
                              name="addressPublicId"
                              value={ this.state.returnDefect.addressPublicId }
                              onChange={ this.handleReturnDefectChange }
                              required
                            />
                          </FormGroup>
                          <FormGroup>
                            <Label>Ont Serial</Label>
                            <Input
                              bsSize="lg"
                              type="text"
                              name="ontSerial"
                              value={ this.state.returnDefect.ontSerial }
                              onChange={ this.handleReturnDefectChange }
                              required
                            />
                          </FormGroup>
                          <FormGroup>
                            <Label>Ont DeviceType</Label>
                            <Input
                              bsSize="lg"
                              type="text"
                              name="ontDeviceType"
                              value={ this.state.returnDefect.ontDeviceType }
                              onChange={ this.handleReturnDefectChange }
                              required
                            />
                          </FormGroup>
                          <FormGroup>
                            <Button color="primary">Submit</Button>
                          </FormGroup>
                        </Form>
                      </Container>
                    </TabPane>

                    {/* RETURNDEFECT */}
                    <TabPane tabId="3">
                      <Container fluid className="p-0 mt-4">
                        <Form onSubmit={ this.handleReturnDefectSubmit } className="mt-4">
                          <FormGroup>
                            <Label>Address Public Id</Label>
                            <Input
                              bsSize="lg"
                              type="text"
                              name="addressPublicId"
                              value={ this.state.returnDefect.addressPublicId }
                              onChange={ this.handleReturnDefectChange }
                              required
                            />
                          </FormGroup>
                          <FormGroup>
                            <Label>Ont Serial</Label>
                            <Input
                              bsSize="lg"
                              type="text"
                              name="ontSerial"
                              value={ this.state.returnDefect.ontSerial }
                              onChange={ this.handleReturnDefectChange }
                              required
                            />
                          </FormGroup>
                          <FormGroup>
                            <Label>Ont DeviceType</Label>
                            <Input
                              bsSize="lg"
                              type="text"
                              name="ontDeviceType"
                              value={ this.state.returnDefect.ontDeviceType }
                              onChange={ this.handleReturnDefectChange }
                              required
                            />
                          </FormGroup>
                          <FormGroup>
                            <Button color="primary">Submit</Button>
                          </FormGroup>
                        </Form>
                      </Container>
                    </TabPane>
                  </TabContent>
              </CardBody>
            </Card>
          </CardBody>
      </React.Fragment>
      );
  }
}

export default TerminateConnection;
