import React from "react";
import DatePicker from "react-datepicker";
import Select from "react-select";
import moment from 'moment';

import { CardHeader, CardBody, Button, Form, FormGroup, Label, Input, CustomInput } from "reactstrap";

import { RegisterAddressConnection, CheckAddressConnectionRegistration, ActivateAddressOnt } from '../../controllers/l2fiber';

class RequestConnection extends React.Component {
  constructor(props) {
    super(props);

    this.state = {
      bandwidth: {
        value: 500,
        label: 500
      },
      bandwidths: [
        { 
          value: 500, 
          label: 500
        },
        { 
          value: 1000, 
          label: 1000
        }
      ],
      connection: {
        addressPublicId: '',
        customerId: '',
        bandwidth: '',
        hasIpTv: false,
        hasCaTv: false,
        option82Label: '',      
        requestedActivationDate: '',
        terminationDate: ''
      }
    };

    // addressPublicId: '3002AA1',
    // customerId: 'FP911138',
    // bandwidth: '',
    // hasIpTv: false,
    // hasCaTv: false,
    // option82Label: 'FP911138',      
    // requestedActivationDate: '28/07/2019',
    // terminationDate: '28/07/2019',

    this.handleChange = this.handleChange.bind(this);
    this.handleSubmit = this.handleSubmit.bind(this);
  }

  handleChange(e) {
    this.setState({ 
      connection: {
          ...this.state.connection,        
          [e.target.name]: e.target.value
        }            
   });
    // if (e.target.name === 'addressPublicId' || 
    //     e.target.name === 'requestedActivationDate' || 
    //     e.target.name === 'terminationDate') {
    //   this.setState({ [e.target.name]: e.target.value });
    // } else {
    //   this.setState({
    //     connection: {
    //       ...this.state.connection,
    //       [e.target.name]: e.target.value
    //     }
    //   })
    // }
    // this.setState({
    //   connection: {
    //     [e.target.name]: e.target.value
    //   }
    // })
  }

  handleSelectChange = (name, value) => {
    let self = this;
    let change = {};
    change[name] = value;
    self.setState(change);
    setTimeout(() => {
      this.setState({
        connection: {
          ...this.state.connection,
          bandwidth: value.value
        }
      });
    }, 500);
  }

  handleDatePickerChange(name, date) {
    this.setState({ [name]: date });
  }

  handleSubmit(e) {
    e.preventDefault();

    this.setState({
      connection: {
        ...this.state.connection,
        bandwidth: this.state.bandwidth.value
      }
    })

    let params = { 
      method : "POST",
      addressPublicId : this.state.connection.addressPublicId,
      customerId : this.state.connection.customerId,
      bandwidth: this.state.bandwidth.value,
      hasIpTv: this.state.connection.hasIpTv,
      hasCaTv: this.state.connection.hasIpTv,
      option82Label: this.state.connection.option82Label,
      requestedActivationDate: moment(this.state.connection.requestedActivationDate).format("MM-DD-YYYY"),
      terminationDate: moment(this.state.connection.terminationDate).format("MM-DD-YYYY"),
    };
      
    (async () => {
      RegisterAddressConnection(params)
      .then(res => {
        // Succesfully registered the Address
        if (res.status == 200) { 

          let params = { 
            method: 'GET',
            addressPublicId : this.state.connection.addressPublicId
          };

          // Check Address Registration Status
          CheckAddressConnectionRegistration(params)
                .then( res => { 
                  
                  // Succesfully received Address Registration status
                  if (res.status == 200) { 

                    /**
                     * {"customerId":"FP911138","bandwidth":500,"hasIpTv":false,"hasCaTv":false,"ontSerial":null,"option82Label":"FP911138","requestedActivationDate":"2019-07-31T00:00:00","activationDate":null,"terminationDate":"2019-07-31T00:00:00","connectionStatus":"Requested","ontDeviceType":null}
                     */
                    
                    let params = { 
                      action : 'Activate',
                      method: 'POST',
                      addressPublicId : this.state.connection.addressPublicId,
                      ontSerial: res.data.ontSerial,
                      ontDeviceType: res.data.ontDeviceType
                    };
                    
                    ActivateAddressOnt(params)
                        .then( res => { 
                          alert(JSON.stringify(res));
                        })
                        .catch(err => {
                          console.log(err.message);
                      });

                  }
                })
                .catch(err => {
                  console.log(err);
              });


        } else { 
          console.error(res);
        }        
      }).catch(err => {
        console.error(err);
      })
    })()
    .catch(err => {
        console.log(err)
    })

    
    // (async () => {      
    //   const path = `${ this.props.domain }/addresses/${ this.state.addressPublicId }`;

    //   await postAPI(`${ path }/connection`, this.state.connection)
    //     .then(res => {
          
    //       (async () => {
    //         await getAPI(`${ path }/connection`)
    //           .then(res => {

    //             (async () => {
    //               const data = {
    //                 ontSerial: res.ontSerial,
    //                 ontDeviceType: res.ontDeviceType
    //               }
    
    //               await postAPI(`${ path }/ont/activate`, data)
    //                 .then(res => {
    //                   console.log(res)
    //                 })
    //                 .catch(err => {
    //                   console.log(err)
    //                 }); 
    //             })()
    //             .catch(err => {
    //               console.log(err)
    //             })
    //           })
    //           .catch(err => {
    //             console.log(err)
    //           }); 
    //       })()
    //       .catch(err => {
    //         console.log(err)
    //       })
    //     })
    //     .catch(err => {
    //       console.log(err)
    //     }); 
    // })()
    // .catch(err => {
    //   console.log(err)
    // })
  }

  render() {
    return (
      <React.Fragment>
          <CardHeader>
              <h4 className="mb-0">Request Connection</h4>
          </CardHeader>
          <CardBody>
            <Form onSubmit={ this.handleSubmit }>
              <FormGroup>
                <Label>Address Public Id</Label>
                <Input
                  bsSize="lg"
                  type="text"
                  name="addressPublicId"
                  value={ this.state.connection.addressPublicId }
                  onChange={ this.handleChange }
                  required
                />
              </FormGroup>
              <FormGroup>
                <Label>Customer Id</Label>
                <Input
                  bsSize="lg"
                  type="text"
                  name="customerId"
                  value={ this.state.connection.customerId }
                  onChange={ this.handleChange }
                  required
                />
              </FormGroup>
              <FormGroup>
                <Label>Bandwidth (KB/s)</Label>
                <Select
                  id="bandwidth"
                  name="bandwidth"
                  className="react-select-container react-select-lg"
                  classNamePrefix="react-select"
                  options={this.state.bandwidths}
                  value={this.state.bandwidth}
                  onChange={this.handleSelectChange.bind(this, 'bandwidth')}
                  maxMenuHeight="100"
                />
                {/* <Input
                  bsSize="lg"
                  type="number"
                  name="bandwidth"
                  value={ this.state.connection.bandwidth }
                  onChange={ this.handleChange }
                /> */}
              </FormGroup>
              <FormGroup>
                <Label>Has Ip Tv</Label>
                <CustomInput
                  bsSize="lg"
                  id="hasIpTvTrue"
                  type="radio"
                  name="hasIpTv"
                  label="True"
                  value="true"
                  checked={ this.state.connection.hasIpTv === true }
                  onChange={ this.handleChange }
                  required
                />
                <CustomInput
                  bsSize="lg"
                  id="hasIpTvFalse"
                  type="radio"
                  name="hasIpTv"
                  label="False"
                  value="false"
                  checked={ this.state.connection.hasIpTv === false }
                  onChange={ this.handleChange }
                  required
                />
              </FormGroup>
              <FormGroup>
                <Label>Has Ca Tv</Label>
                <CustomInput
                  bsSize="lg"
                  id="hasCaTvTrue"
                  type="radio"
                  name="hasCaTv"
                  label="True"
                  value="true"
                  checked={ this.state.connection.hasCaTv === true }
                  onChange={ this.handleChange }
                  required
                />
                <CustomInput
                  bsSize="lg"
                  id="hasCaTvFalse"
                  type="radio"
                  name="hasCaTv"
                  label="False"
                  value="false"
                  checked={ this.state.connection.hasCaTv === false }
                  onChange={ this.handleChange }
                  required
                />
              </FormGroup>
              <FormGroup>
                <Label>Option 82 Label</Label>
                <Input
                  bsSize="lg"
                  type="text"
                  name="option82Label"
                  value={ this.state.connection.option82Label }
                  onChange={ this.handleChange }
                  required
                />
              </FormGroup>
              <FormGroup>
                <Label>Requested Activation Date</Label>
                <Input
                    bsSize="lg"
                    type="date"
                    name="requestedActivationDate"
                    value={this.state.connection.requestedActivationDate}
                    onChange={this.handleChange}
                    required
                />
                {/* <DatePicker
                  className="form-control form-control-lg"
                  name="requestedActivationDate"
                  dateFormat="dd/MM/yyyy"
                  autoComplete="off"
                  selected={ this.state.rawRequestedActivationDate }
                  onChange={this.handleDatePickerChange.bind(this, 'rawRequestedActivationDate')}
                /> */}
              </FormGroup>
              <FormGroup>
                <Label>Termination Date</Label>
                <Input
                    bsSize="lg"
                    type="date"
                    name="terminationDate"
                    value={this.state.connection.terminationDate}
                    onChange={this.handleChange}
                    required
                />
                {/* <DatePicker
                  className="form-control form-control-lg"
                  name="terminationDate"
                  dateFormat="dd/MM/yyyy"
                  autoComplete="off"
                  selected={ this.state.rawTerminationDate }
                  onChange={this.handleDatePickerChange.bind(this, 'rawTerminationDate')}
                /> */}
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

export default RequestConnection;
