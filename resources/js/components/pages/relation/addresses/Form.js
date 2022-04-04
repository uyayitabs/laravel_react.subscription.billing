import React from "react";
import Select from "react-select";

import { Button, Modal, ModalBody, ModalFooter, ModalHeader, Label, FormGroup, Row, Col, Form } from "reactstrap";
import { AvForm, AvInput } from "availity-reactstrap-validation";

import { GetAddressDependencies, GetCountries, GetStates, GetCities, AddAddress, UpdateAddress } from '../../../controllers/relations';

class AddressForm extends React.Component {
  constructor(props) {
    super(props);

    this.state = {
      address: this.props.selectedData ? this.props.selectedData : {},
      formType: '',
      formName: 'Address',
      country_id: null,
      hasCountry: false,
      city_id: null,
      cityPlace: 'Select Country first'
    };

    this.toggle = this.toggle.bind(this)
    this.handleInputChange = this.handleInputChange.bind(this)
    this.handleSubmit = this.handleSubmit.bind(this)
    this.hasCountry = this.hasCountry.bind(this)
    // this.hasState = this.hasState.bind(this)
  }

  toggle() {
    this.props.hide()
  }

  handleInputChange(e) {
    e.persist()

    let address = Object.assign({}, this.state.address)

    address[e.target.name] = e.target.value

    this.setState({ address })
  }

  handleSelectChange(name, value) {
    if (name === 'country_id') {
      this.setState({
        hasCountry: false,
        city_id: null,
        cityOpts: []
      })
    }

    let address = Object.assign({}, this.state.address)

    address[name] = value.value    

    this.setState({ 
        address, 
        [name]: value
    })
  }

  hasCountry() {
    if (!this.state.hasCountry) {
      this.setState({
        hasCountry: true,
        cityPlace: 'City'
      });

      (async () => {
        await GetCities(this.state.country_id.value)
          .then(res => {

            const cities = res.data.data
            let cityOpts = []

            cities.forEach((city) => {
              cityOpts.push({
                label: city.name,
                value: city.id
              })
            })

            this.setState({ cityOpts })
            
            const { selectedData } = this.props

            if (selectedData && selectedData.city_id) {
              const getCity = cityOpts.filter(city => parseInt(city.value) === parseInt(selectedData.city_id))

              this.setState({ city_id: getCity[0] })
            }
          })
          .catch(err => {
            console.log(err.response.data)
          })
      })().catch(err => {
        console.log(err)
      })
    }
  }

  handleSubmit(e) {
    e.persist();
    
    if (this.props.selectedData) {
      this.state.address._method = 'PATCH';

      (async () => {
        await UpdateAddress(this.state.address.id, this.state.address)
          .then(res => {
            this.toggle()
            
            this.props.update(this.state.address, this.props.selectedDataRow)
          })
          .catch(err => {
            console.log(err)
          });
      })()
      .catch(err => {
        console.log(err)
      })
    } else {
      this.state.address.relation_id = this.props.id;
        
      (async () => {
        await AddAddress(this.props.id, this.state.address)
          .then(res => {
            this.toggle();

            let data = res.data.data;
            const getType = this.state.addressTypeOpts.filter(type => parseInt(type.value) === parseInt(data.address_type_id))

            data.address_type = {
              id: data.address_type_id,
              type: getType[0].label
            }

            this.props.update(data, null);
          })
          .catch(err => {
            console.log(err)
          });
      })()
      .catch(err => {
        console.log(err)
      })
    }
  }

  componentDidMount() {
    this.setState({ formType: this.props.selectedData ? 'Edit' : 'New' });

    (async () => {
      await GetAddressDependencies()
        .then(res => {
          const datas = res.data,
                address_types = datas.address_types.data

          let addressTypeOpts = [];
          address_types.forEach((address_type) => {
            addressTypeOpts.push({
              label: address_type.type,
              value: address_type.id
            });
          });
          this.setState({ addressTypeOpts })

          const { selectedData } = this.props

          if (selectedData && selectedData.address_type_id) {
            const getType = addressTypeOpts.filter(type => parseInt(type.value) === parseInt(selectedData.address_type_id))

            this.setState({ address_type_id: getType[0] })
          }

          (async () => {
            await GetCountries()
              .then(res => {
                const countries = res.data.data
                let countryOpts = []

                countries.forEach((country) => {
                  countryOpts.push({
                    label: country.name,
                    value: country.id
                  })
                })

                this.setState({ countryOpts })

                if (selectedData && selectedData.country_id) {
                  const getCountry = countryOpts.filter(country => parseInt(country.value) === parseInt(selectedData.country_id))

                  this.setState({ country_id: getCountry[0] })
                }
              })
              .catch(err => {
                console.log(err.response.data)
              })
          })().catch(err => {
            console.log(err)
          })
        })
        .catch(err => {
          console.log(err)
        });
    })()
    .catch(err => {
      console.log(err)
    })
  }

  render() {
    return (
      <Modal
          isOpen={ this.props.show }
          toggle={ this.toggle }
          centered
          backdrop="static"
        >
          <AvForm onSubmit={ this.handleSubmit }>
            <ModalHeader>{this.state.formType} {this.state.formName}</ModalHeader>
            <ModalBody className="mt-3 mb-3">
              <Row>
                <Col md={2}>Address:</Col>
                <Col md={10}>
                  <FormGroup className="row">
                    <Col md={8}>
                      <AvInput 
                        name="street1" 
                        placeholder="Street 1" 
                        value={ this.state.address.street1 || null } 
                        onChange={ this.handleInputChange } 
                      />
                    </Col>
                    <Col md={4}>
                      <AvInput 
                        name="house_number" 
                        placeholder="House no" 
                        value={ this.state.address.house_number || null } 
                        onChange={ this.handleInputChange } 
                      />
                    </Col>
                  </FormGroup>

                  <FormGroup className="row">
                    <Col md={8}>
                      <AvInput 
                        name="street2" 
                        placeholder="Street 2" 
                        value={ this.state.address.street2 || null } 
                        onChange={ this.handleInputChange } 
                      />
                    </Col>
                    <Col md={4}>
                      <AvInput 
                        name="room" 
                        placeholder="Room no" 
                        value={ this.state.address.room || null } 
                        onChange={ this.handleInputChange } 
                      />
                    </Col>
                  </FormGroup>

                  <FormGroup className="row">
                    <Col md={4}>
                      <Select
                        className="react-select-container"
                        classNamePrefix="react-select"
                        placeholder= 'Type'
                        options={ this.state.addressTypeOpts }
                        value={ this.state.address_type_id }
                        onChange={ this.handleSelectChange.bind(this, 'address_type_id') }
                        maxMenuHeight="100"
                      />
                    </Col>
                    <Col md={8}>
                      <AvInput 
                        name="zipcode" 
                        placeholder="Zipcode" 
                        value={ this.state.address.zipcode || null } 
                        onChange={ this.handleInputChange } 
                      />
                    </Col>
                  </FormGroup>

                  <FormGroup className="row">
                    <Col md={4}>
                      <Select
                        className="react-select-container"
                        classNamePrefix="react-select"
                        placeholder= 'Country'
                        options={ this.state.countryOpts }
                        value={ this.state.country_id }
                        onChange={ this.handleSelectChange.bind(this, 'country_id') }
                        maxMenuHeight="100"
                      />
                    </Col>
                    { this.state.country_id ? this.hasCountry() : null }
                    <Col md={8}>
                      <Select
                        className="react-select-container"
                        classNamePrefix="react-select"
                        placeholder= { this.state.cityPlace }
                        options={ this.state.cityOpts }
                        value={ this.state.city_id }
                        onChange={ this.handleSelectChange.bind(this, 'city_id') }
                        maxMenuHeight="100"
                        isDisabled={ !this.state.hasCountry }
                      />
                    </Col>
                  </FormGroup>
                </Col>
              </Row>
            </ModalBody>
            <ModalFooter className="justify-content-between">
              <span className="btn btn-danger" onClick={ this.toggle }>Cancel</span>
              <Button color="primary">Submit</Button>
            </ModalFooter>
          </AvForm>
        </Modal>
    );
  }
}

export default AddressForm;
