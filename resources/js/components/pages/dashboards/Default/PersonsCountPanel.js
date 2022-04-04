import React from "react";

import { Card, CardBody, Media } from "reactstrap";
import { User } from "react-feather";

import { GetPersonsCount } from '../../../controllers/dashboard';

class PersonsCountPanel extends React.Component {
    isMounted = false;

    constructor() {
        super();

        this.state = {
            persons: '0',
            currentTenant: sessionStorage.getItem('tenant_id') ? sessionStorage.getItem('tenant_id') : null
        }

        this.checkTenantChange = this.checkTenantChange.bind(this)
        this.updateData = this.updateData.bind(this)     
    }
    
    getData() {
        (async () => {
            await GetPersonsCount()
                .then(res => {
                    const persons = res.data.data;

                    this.setState({ persons });
                })
                .catch(err => {
                    console.log(err)
                });
        })()
        .catch(err => {
            console.log(err)
        })
    }

    checkTenantChange() {
        if ((this.state.currentTenant === sessionStorage.getItem('tenant_id')) || (parseInt(this.state.currentTenant) === parseInt(sessionStorage.getItem('tenant_id')))) {
            setTimeout(this.checkTenantChange, 100)
        } else {
            this.setState({ 
                currentTenant: sessionStorage.getItem('tenant_id'),
                loading: true
            })
                
            setTimeout(this.updateData, 100)
        }
    }

    updateData() {
        if (this.isMounted) {
            this.getData()
            this.checkTenantChange()
        }
    }

    componentDidMount() {
        this.isMounted = true;
        this.updateData()
    }

    componentWillUnmount() {
        this.isMounted = false;
    }

    render() {
        return (
            <Card className="flex-fill">
                <CardBody className="py-4">
                    <Media>
                        <div className="d-inline-block mt-2 mr-3">
                            <User className="feather-lg text-warning" />
                        </div>
                        <Media body>
                            <h3 className="mb-2">{this.state.persons}</h3>
                            <div className="mb-0">Persons</div>
                        </Media>
                    </Media>
                </CardBody>
            </Card>
        );
    };
}

export default PersonsCountPanel;