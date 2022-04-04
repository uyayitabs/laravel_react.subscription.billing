import React from "react";
import Select from "react-select";
import DatePicker from "react-datepicker";
import moment from 'moment';
import ReactQuill from "react-quill";

import { Form, Input, CustomInput, Button, ListGroup, ListGroupItem, Card, CardHeader, Badge, Col, CardBody, UncontrolledDropdown, DropdownToggle, DropdownMenu, DropdownItem } from "reactstrap";
import { MoreHorizontal, Check, X } from "react-feather";

import Loader from './Loader';

class Details extends React.Component {  
  constructor(props) {
    super(props)

    this.state = {
        columns: [ { count: 1 }, { count: 2 } ],
        onEdit: false,
        loading: true
    }

    this.renderList = this.renderList.bind(this)
    this.toggleForm = this.toggleForm.bind(this)
    this.selectDataFormatter = this.selectDataFormatter.bind(this)
    this.formatDotNotation = this.formatDotNotation.bind(this)
    this.getDotNotation = this.getDotNotation.bind(this)
    this.formatDate = this.formatDate.bind(this)
    this.handleQuillChange = this.handleQuillChange.bind(this)
  }

  toggleEdit(e, bool) {
    if (this.state.onEdit && !bool) {
        this.setState({
            data: JSON.parse(JSON.stringify(this.state.prev)),
            inputData: JSON.parse(JSON.stringify(this.state.prevInput))
        })
    } else {
        this.setState({
            prev: JSON.parse(JSON.stringify(this.state.data)),
            prevInput: JSON.parse(JSON.stringify(this.state.inputData))
        })
    }

    this.setState({ onEdit: !this.state.onEdit })
  }

  toggleForm() {
      this.props.toggleForm()
  }

  handleInputChange(e) {
    e.preventDefault()

    let data = Object.assign({}, this.state.data),
        inputData = Object.assign({}, this.state.inputData)

    const { name, value } = event.target
        
    if (name.split('.').length > 1) {
        const newDataValue = this.getDotNotation(name, data, value),
              newInputDataValue = this.getDotNotation(name, inputData, value)

        data = newDataValue
        inputData = newInputDataValue
    } else {
        data[name] = value
        inputData[name] = value
    }
    
    this.setState({ 
        data, 
        inputData
    })
}

  handleCheckChange(e, name) {
    let data = Object.assign({}, this.state.data),
    inputData = Object.assign({}, this.state.inputData)

    if (name.split('.').length > 1) {
        const newDataValue = this.getDotNotation(name, data, e.target.checked),
              newInputDataValue = this.getDotNotation(name, inputData, e.target.checked)

        data = newDataValue
        inputData = newInputDataValue
    } else {
        data[name] = e.target.checked
        inputData[name] = e.target.checked
    }

    this.setState({
        data,
        inputData
    })
  }

  handleSelectChange(name, value) {
    let data = Object.assign({}, this.state.data),
        inputData = Object.assign({}, this.state.inputData)

    if (name.split('.').length > 1) {
        const newDataValue = this.getDotNotation(name, data, value.label),
              newInputDataValue = this.getDotNotation(name, inputData, value)

        data = newDataValue
        inputData = newInputDataValue
    } else {        
        data[name] = value.label
        inputData[name] = value
    }

    this.setState({
        data,
        inputData
    })
  }

  handleDatePickerChange(name, date) {
    let data = Object.assign({}, this.state.data),
        inputData = Object.assign({}, this.state.inputData)

    if (name.split('.').length > 1) {
        const newDataValue = this.getDotNotation(name, data, moment(date).format('DD-MM-YYYY')),
              newInputDataValue = this.getDotNotation(name, inputData, date)

        data = newDataValue
        inputData = newInputDataValue
    } else {
        data[name] = moment(date).format('DD-MM-YYYY')
        inputData[name] = date
    }

    this.setState({
        data,
        inputData
    })
  }

  handleQuillChange(val) {
    let data = Object.assign({}, this.state.data),
        inputData = Object.assign({}, this.state.inputData)

    const name = this.props.quill

    if (name.split('.').length > 1) {
        const newDataValue = this.getDotNotation(name, data, val),
              newInputDataValue = this.getDotNotation(name, inputData, val)

        data = newDataValue
        inputData = newInputDataValue
    } else {
        data[name] = val
        inputData[name] = val
    }

    this.setState({
        data,
        inputData
    })
  }

  handleSubmit(e) {
    e.preventDefault()
    const { updateApi, id } = this.props;

    (async () => {
        await updateApi(id, this.selectDataFormatter())
            .then(res => {
                const inputData = JSON.parse(JSON.stringify(this.state.inputData))

                this.setState({ prevInput: inputData })
                this.toggleEdit(null, true)
            })
            .catch(err => {
                console.log(err)
            });
    })().catch(err => {
        console.log(err)
    })
  }

  selectDataFormatter() {
    const { details } = this.props,
          inputData = this.state.inputData
    let data = this.state.data,
        detail = ''

    details.map((item, index) => {
        const path = item.data
        
        if (item.type === 'select' && this.getDotNotation(path, inputData) !== null) {
            if (path.split('.').length === 1) {
                const selectedData = inputData[path].value
                data[path] = selectedData
            } else {
                const selectedData = this.getDotNotation(path, inputData).value
                const newValue = this.getDotNotation(path, data, selectedData)
                
                data = newValue
            }
        }
        
        if (index > 0) {
            detail = `${ detail },${ item.data }`
        } else {
            detail = item.data
        }
    })

    detail = detail.split(',')

    let dataHolder = ''

    let newData = JSON.parse(JSON.stringify(data))

    for (let [resItem] of Object.entries(data)) {
        details.map((item) => {
            const path = item.data,
                  pathLength = path.split('.')

            if (item.disabled === true && dataHolder.split(',').indexOf(path) < 0) {
                delete newData[path]
                dataHolder = `${ dataHolder },${ path }`
            } else if ((!this.props.quill && detail.indexOf(resItem) < 0) || ((this.props.quill && resItem !== this.props.quill) && detail.indexOf(resItem) < 0)) {
                if (pathLength.length === 1) {
                    delete newData[resItem]
                } else {
                    if (dataHolder.split(',').indexOf(pathLength[0]) < 0) {
                        delete newData[pathLength[0]]
                        dataHolder = `${ dataHolder },${ pathLength[0] }`
                    }
                }
            }
        })
    }

    newData._method = 'PATCH'

    this.setState({ data })
    return newData
  }

    formatDotNotation(path, obj, val) {
        const thisPath = path.split('.'),
              [bodyPath] = thisPath.slice(0, -1),
              [pathTail] = thisPath.slice(-1),
              thisObj = thisPath.slice(0, -1).reduce((item, i) => (item === undefined ? undefined : item[i]), obj)

        if (thisObj !== undefined) {
            if (val) {
                thisObj[pathTail] = val
                obj[bodyPath] = thisObj
                return obj
            } else {
                return thisObj[pathTail]
            }
        }
    }

    getDotNotation(path, obj, val) {
        let thisPath = path.split('.')

        if (thisPath.length > 1) {
            const [bodyPath] = thisPath.slice(0, -1),
                  [pathTail] = thisPath.slice(-1),
                  thisObj = thisPath.slice(0, -1).reduce((item, i) => (item === undefined ? undefined : item[i]), obj)

            if (thisObj !== undefined && thisObj != null) {
                if (val) {
                    thisObj[pathTail] = val
                    obj[bodyPath] = thisObj
                    return obj
                } else {
                    return thisObj[pathTail]
                }
            }            
        } else {
            return path.split('.').reduce((item, i) => item && item[i] ? item[i] : item[i] === 0 ? item[i].toString() : null, obj)
        }
    }

    formatDate(date) {
        const parts = date.split('-')
        
        return new Date(`${ parts[2] }-${ parts[1] }-${ parts[0] }`)
    }

  renderList(item) {
    const { data, inputData } = this.state,
          path = item.data

    return (
        <ListGroupItem className="d-flex align-items-center px-0">
            <Col xs="6" md="5" lg="4" style={{ lineHeight: 2.3 }}>{ item.label }</Col>
            <Col xs="6" md="7" lg="8">
                { this.state.onEdit && !item.disabled ?
                    item.type === 'select' ?
                        <Select
                            className="react-select-container"
                            classNamePrefix="react-select"
                            options={ item.opts }
                            value={ this.getDotNotation(path, inputData) }
                            onChange={ this.handleSelectChange.bind(this, path) }
                            maxMenuHeight="100"
                        /> :
                    item.type === 'datepicker' ?
                        <DatePicker
                            className="form-control"
                            name={ path }
                            dateFormat="dd/MM/yyyy"
                            autoComplete="off"
                            selected={ this.getDotNotation(path, inputData) === null ? this.getDotNotation(path, inputData) : new Date(this.getDotNotation(path, inputData)) }
                            onChange={ this.handleDatePickerChange.bind(this, path) }
                        /> :                    
                    item.type === 'checkbox' ?
                        <CustomInput
                            id={ path }
                            type="checkbox"
                            name={ path }
                            defaultChecked={ this.getDotNotation(path, inputData) || parseInt(this.getDotNotation(path, inputData)) === 1 ? true : false }
                            onChange={ (e) => this.handleCheckChange(e, path) }
                        /> :
                        <Input
                            type={ item.type }
                            name={ path }
                            value={ this.getDotNotation(path, inputData) }
                            onChange={ this.handleInputChange.bind(this) }
                        /> : 
                    path === 'status' ? 
                        <Badge
                            color={ this.getDotNotation(path, data) === 1 ? 'success' : 'danger' }
                            className="badge-pill mr-1 mb-1"
                        >
                            { this.getDotNotation(path, data) === 1 ? 'Active' : 'Inactive' }
                        </Badge> :
                            item.type === 'select' && this.getDotNotation(path, data) !== null && this.getDotNotation(path, data) != undefined ?
                                path.split('.').length === 1 && item.opts[item.opts.findIndex(i => i.value.toString() === this.getDotNotation(path, data).toString())] != undefined ? 
                                    item.opts[item.opts.findIndex(i => i.value.toString() === this.getDotNotation(path, data).toString())].label : 
                                    this.getDotNotation(path, inputData).label :
                            item.type === 'checkbox' ?
                            this.getDotNotation(path, data) ? <Check size={ 18 } color="#47bac1" /> : <X size={ 18 } color="#f44455" />  : 
                            this.getDotNotation(path, data)
                }
            </Col>
        </ListGroupItem>
    )
  }

  componentDidMount() {
    const { getApi, id, include, details } = this.props;

    (async () => {
        await getApi({ include }, id)
            .then(res => {
                let data = res.data.data

                this.setState({
                    data: JSON.parse(JSON.stringify(data)),
                    inputData: JSON.parse(JSON.stringify(data))
                })

                let inputData = JSON.parse(JSON.stringify(data))

                if (details !== undefined) {
                    details.map((item) => {
                        const path = item.data.split('.')
                        
                        if (item.type === 'select' && this.getDotNotation(item.data, inputData) !== null) {                            
                            if (path.length > 1) {
                                const selectedData = item.opts.filter(i => i.value === this.getDotNotation(item.data, inputData)),
                                      newValue = this.getDotNotation(item.data, inputData, selectedData[0])
    
                                    inputData = newValue
                            } else {
                                const selectedData = item.opts.filter(i => i.value.toString() === inputData[item.data].toString())
                                
                                inputData[item.data] = selectedData[0]
                            }
                        } else if (item.type === 'datepicker') {
                            if (path.length > 1) {
                                if (this.getDotNotation(item.data, inputData) !== null) {
                                    const val = this.formatDate(this.getDotNotation(item.data, inputData)),
                                          newValue = this.getDotNotation(item.data, inputData, val)
    
                                    inputData = newValue
                                } else {
                                    const newValue = this.getDotNotation(item.data, inputData, null)
        
                                    inputData = newValue
                                }
                            } else {
                                if (inputData[item.data] !== null) {
                                    inputData[item.data] = this.formatDate(inputData[item.data])
                                } else {
                                    inputData[item.data] = null
                                }
                            }                        
                        } else if (item.type === 'text' && this.getDotNotation(item.data, inputData) === null) {
                            if (path.length > 1) {
                                const newValue = this.getDotNotation(item.data, inputData, '')
    
                                inputData = newValue
                            } else {
                                inputData[item.data] = ''
                            }                        
                        }
                    })
                }

                this.setState({
                    inputData,
                    loading: false
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
    const { loading, columns, title } = this.state,
          { details, dropdownItems } = this.props

    return (
        <React.Fragment>
            { !loading ?
                <React.Fragment>
                    <Card>
                        <CardHeader className="d-flex align-items-center">
                            { title !== undefined ? <h3 className="mb-0">{ title }</h3> : null }
                            <UncontrolledDropdown className="ml-auto">
                                <DropdownToggle nav className="px-3 py-2">
                                    <MoreHorizontal size={ 18 } />
                                </DropdownToggle>

                                <DropdownMenu right={ true }>
                                    <DropdownItem className="py-2" onClick={ (e) => this.toggleEdit(e, false) }>
                                        { this.state.onEdit ? 'Cancel' : 'Edit' }
                                    </DropdownItem>
                                    { dropdownItems !== undefined ?
                                        dropdownItems.map((item, index) => {
                                            return (
                                                <DropdownItem className="py-2" onClick={ this[item.function] } key={ index }>
                                                    { item.label }
                                                </DropdownItem>
                                            )
                                        }) : null
                                    }
                                </DropdownMenu>
                            </UncontrolledDropdown>
                        </CardHeader>
                        <CardBody>
                            <Form className="row" onSubmit={ (e) => this.handleSubmit(e) }>
                                { !loading ?
                                    columns.map((item1, index1) => {
                                        return (
                                            <Col xs="12" md="6" key={ index1 }>
                                                <ListGroup flush>
                                                    {
                                                        details.map((item2, index2) => {
                                                            return (
                                                                <React.Fragment key={ index2 }>
                                                                    {  index1 === 0 && index2 < Math.floor(details.length / 2) ?
                                                                        this.renderList(item2) :
                                                                        index1 === 1 && index2 >= Math.floor(details.length / 2) ?
                                                                        this.renderList(item2) : null
                                                                    }
                                                                </React.Fragment>
                                                            )
                                                        })
                                                    }
                                                </ListGroup>
                                            </Col>
                                        )
                                    }) : null
                                }
                                { !loading && this.props.quill ?
                                    <Col xs="12">
                                        <ReactQuill 
                                            placeholder={ `${ this.props.quill }...` } 
                                            value={ this.state.inputData[this.props.quill] } 
                                            onChange={ this.handleQuillChange } 
                                            readOnly={ !this.state.onEdit } 
                                        />
                                    </Col> : null
                                }
                                { this.state.onEdit ?
                                    <Col xs="12" className="d-flex justify-content-between pt-3">
                                        <Col xs="auto" className="px-0">
                                            <Button color="danger" onClick={ (e) => this.toggleEdit(e, false) }> Cancel </Button>
                                        </Col>
                                        <Col xs="auto" className="px-0">
                                            <Button color="primary"> Save </Button>
                                        </Col>
                                    </Col> : null
                                }
                            </Form>
                        </CardBody>
                    </Card>                
                </React.Fragment> : <Loader />
            }
        </React.Fragment>
    )
  }
}

export default Details;